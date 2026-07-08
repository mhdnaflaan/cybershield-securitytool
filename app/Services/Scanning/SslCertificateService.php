<?php

namespace App\Services\Scanning;

use App\DTOs\SslReportDto;
use App\Services\Contracts\SslServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SslCertificateService implements SslServiceInterface
{
    /**
     * Check SSL certificate and headers with caching
     */
    public function check(string $domain): SslReportDto
    {
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = rtrim($domain, '/');

        // Cache key for 1 hour
        $cacheKey = 'ssl_check_' . md5($domain);

        return Cache::remember($cacheKey, 3600, function () use ($domain) {
            return $this->performCheck($domain);
        });
    }

    /**
     * Perform the actual SSL check
     */
    private function performCheck(string $domain): SslReportDto
    {
        $headers = [];
        $warnings = [];
        $error = null;
        $hasSsl = false;
        $isValid = false;
        $protocolVersion = null;
        $cipherSuite = null;
        $issuer = 'N/A';
        $subject = 'N/A';
        $expiryDate = null;
        $daysLeft = null;
        $certificateChain = [];

        try {
            // ============================================
            // 1. REAL TLS HANDSHAKE
            // ============================================
            $streamContext = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    'capture_peer_cert' => true,
                    'capture_peer_cert_chain' => true,
                ]
            ]);

            $client = @stream_socket_client(
                'ssl://' . $domain . ':443',
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $streamContext
            );

            if (!$client) {
                throw new \Exception("Could not connect: $errstr ($errno)");
            }

            // Get SSL parameters
            $params = stream_context_get_params($client);

            // Get protocol version
            $streamMeta = stream_get_meta_data($client);
            $protocolVersion = $streamMeta['crypto_method'] ?? null;

            // Map crypto method to readable name
            $protocolVersion = $this->mapProtocolVersion($protocolVersion);

            // Get cipher suite
            if (function_exists('stream_socket_get_cipher')) {
                $cipherSuite = stream_socket_get_cipher($client);
            }

            // Get certificate
            $cert = $params['options']['ssl']['peer_certificate'] ?? null;
            $certChain = $params['options']['ssl']['peer_certificate_chain'] ?? [];

            if (!$cert) {
                throw new \Exception('No SSL certificate found');
            }

            $certInfo = openssl_x509_parse($cert);

            if (!$certInfo) {
                throw new \Exception('Could not parse certificate');
            }

            $hasSsl = true;
            $isValid = true;
            $issuer = $certInfo['issuer']['CN'] ?? $certInfo['issuer']['O'] ?? 'Unknown';
            $subject = $certInfo['subject']['CN'] ?? 'Unknown';

            // Certificate chain
            foreach ($certChain as $chainCert) {
                $chainInfo = openssl_x509_parse($chainCert);
                if ($chainInfo) {
                    $certificateChain[] = [
                        'subject' => $chainInfo['subject']['CN'] ?? 'Unknown',
                        'issuer' => $chainInfo['issuer']['CN'] ?? 'Unknown',
                    ];
                }
            }

            // Expiry
            if (isset($certInfo['validTo_time_t'])) {
                $expiryTimestamp = $certInfo['validTo_time_t'];
                $expiryDate = date('Y-m-d H:i:s', $expiryTimestamp);
                $daysLeft = floor(($expiryTimestamp - time()) / 86400);

                if ($daysLeft <= 0) {
                    $isValid = false;
                    $warnings[] = 'SSL certificate has expired.';
                } elseif ($daysLeft <= 7) {
                    $warnings[] = "SSL certificate expires in {$daysLeft} days. Please renew soon.";
                } elseif ($daysLeft <= 30) {
                    $warnings[] = "SSL certificate expires in {$daysLeft} days. Consider renewing.";
                }
            }

            fclose($client);

            // ============================================
            // 2. COMPLETE HEADER AUDIT
            // ============================================
            $headers = $this->fetchHeaders($domain);
            $headerWarnings = $this->analyzeHeaders($headers);
            $warnings = array_merge($warnings, $headerWarnings);

        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('SSL Check Failed', [
                'domain' => $domain,
                'error' => $error,
            ]);
        }

        return new SslReportDto(
            hasSsl: $hasSsl,
            isValid: $isValid,
            protocolVersion: $protocolVersion,
            cipherSuite: $cipherSuite,
            issuer: $issuer,
            subject: $subject,
            expiryDate: $expiryDate,
            daysLeft: $daysLeft,
            certificateChain: $certificateChain,
            headers: $headers,
            warnings: $warnings,
            error: $error,
            rawData: null,
        );
    }

    /**
     * Map crypto method to readable protocol name
     */
    private function mapProtocolVersion(?int $cryptoMethod): ?string
    {
        if ($cryptoMethod === null) {
            return null;
        }

        $map = [
            STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT => 'TLSv1.0',
            STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT => 'TLSv1.1',
            STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT => 'TLSv1.2',
            STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT => 'TLSv1.3',
        ];

        return $map[$cryptoMethod] ?? 'Unknown';
    }

    /**
     * Fetch security headers with redirect following
     */
    private function fetchHeaders(string $domain): array
    {
        $headers = [
            'hsts' => false,
            'csp' => false,
            'x_frame_options' => false,
            'x_content_type_options' => false,
            'referrer_policy' => false,
            'permissions_policy' => false,
            'x_xss_protection' => false,
            'raw' => [],
        ];

        try {
            $url = 'https://' . $domain;

            // Use cURL with follow redirects
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 0 || !empty($error)) {
                return $headers;
            }

            // Parse headers
            $parsedHeaders = [];
            $lines = explode("\r\n", $response);
            foreach ($lines as $line) {
                if (strpos($line, ': ') !== false) {
                    list($key, $value) = explode(': ', $line, 2);
                    $parsedHeaders[strtolower($key)] = trim($value);
                }
            }

            $headers['raw'] = $parsedHeaders;

            // Check each header
            $headers['hsts'] = isset($parsedHeaders['strict-transport-security']);
            $headers['csp'] = isset($parsedHeaders['content-security-policy']) ||
                              isset($parsedHeaders['content-security-policy-report-only']);
            $headers['x_frame_options'] = isset($parsedHeaders['x-frame-options']);
            $headers['x_content_type_options'] = isset($parsedHeaders['x-content-type-options']) &&
                strtolower($parsedHeaders['x-content-type-options']) === 'nosniff';
            $headers['referrer_policy'] = isset($parsedHeaders['referrer-policy']);
            $headers['permissions_policy'] = isset($parsedHeaders['permissions-policy']);
            $headers['x_xss_protection'] = isset($parsedHeaders['x-xss-protection']);

            return $headers;

        } catch (\Exception $e) {
            Log::warning('Header fetch failed', [
                'domain' => $domain,
                'error' => $e->getMessage(),
            ]);
            return $headers;
        }
    }

    /**
     * Analyze headers and return actionable warnings
     */
    private function analyzeHeaders(array $headers): array
    {
        $warnings = [];

        if (!$headers['hsts']) {
            $warnings[] = 'HSTS header is missing. This allows downgrade attacks.';
        }

        if (!$headers['csp']) {
            $warnings[] = 'Content-Security-Policy (CSP) header is missing. This makes the site vulnerable to XSS attacks.';
        }

        if (!$headers['x_frame_options']) {
            $warnings[] = 'X-Frame-Options header is missing. This allows clickjacking attacks.';
        }

        if (!$headers['x_content_type_options']) {
            $warnings[] = 'X-Content-Type-Options header is missing. This allows MIME sniffing attacks.';
        }

        if (!$headers['referrer_policy']) {
            $warnings[] = 'Referrer-Policy header is missing. This can leak sensitive information.';
        }

        if (!$headers['permissions_policy']) {
            $warnings[] = 'Permissions-Policy header is missing. This allows unwanted browser features.';
        }

        if (!$headers['x_xss_protection']) {
            $warnings[] = 'X-XSS-Protection header is missing. This allows XSS attacks to go unfiltered.';
        }

        return $warnings;
    }
}