<?php

namespace App\Services\ThreatIntel;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VirusTotalService
{
    public function checkUrl(string $url): array
    {
        $apiKey = config('services.virustotal.api_key');

        if (empty($apiKey)) {
            return ['error' => 'VirusTotal API key not configured'];
        }

        $cacheKey = 'vt_' . md5($url);

        return Cache::remember($cacheKey, 1800, function () use ($url, $apiKey) {
            return $this->performCheck($url, $apiKey);
        });
    }

    private function performCheck(string $url, string $apiKey): array
    {
        try {
            $encodedUrl = urlencode($url);

            // Step 1: Try to get existing report
            $response = Http::withHeaders([
                'x-apikey' => $apiKey,
            ])->timeout(20)->get("https://www.virustotal.com/api/v3/urls/{$encodedUrl}");

            // Step 2: If 404, submit URL for scanning
            if ($response->status() === 404) {
                Log::info('VirusTotal: URL not found, submitting for scan', ['url' => $url]);

                $submitResponse = Http::withHeaders([
                    'x-apikey' => $apiKey,
                ])->asForm()->timeout(20)->post('https://www.virustotal.com/api/v3/urls', [
                    'url' => $url,
                ]);

                if ($submitResponse->failed()) {
                    return ['error' => 'Failed to submit URL: ' . $submitResponse->status()];
                }

                $analysisId = $submitResponse->json('data.id');

                // Step 3: Poll for completion (max 8 attempts, 4 seconds each = 32 seconds total)
                $attempts = 0;
                $maxAttempts = 8;
                $analysisComplete = false;
                $analysisResult = null;

                while ($attempts < $maxAttempts) {
                    sleep(4);
                    $analysisResponse = Http::withHeaders([
                        'x-apikey' => $apiKey,
                    ])->timeout(20)->get("https://www.virustotal.com/api/v3/analyses/{$analysisId}");

                    if ($analysisResponse->successful()) {
                        $status = $analysisResponse->json('data.attributes.status');
                        if ($status === 'completed') {
                            $analysisComplete = true;
                            $analysisResult = $analysisResponse;
                            break;
                        } elseif ($status === 'failed') {
                            return ['error' => 'VirusTotal analysis failed for this URL.'];
                        }
                    }
                    $attempts++;
                }

                if (!$analysisComplete) {
                    return [
                        'error' => 'Analysis is still in progress. Please try again in a few minutes.',
                        'pending' => true,
                    ];
                }

                // Step 4: Get the final report
                $reportResponse = Http::withHeaders([
                    'x-apikey' => $apiKey,
                ])->timeout(20)->get("https://www.virustotal.com/api/v3/urls/{$encodedUrl}");

                if ($reportResponse->successful()) {
                    $response = $reportResponse;
                } else {
                    return ['error' => 'Failed to get report: ' . $reportResponse->status()];
                }
            }

            // Step 5: Parse the response
            if (!$response->successful()) {
                return ['error' => 'VirusTotal API request failed: ' . $response->status()];
            }

            $data = $response->json();
            $attributes = $data['data']['attributes'] ?? [];
            $stats = $attributes['last_analysis_stats'] ?? [];
            $results = $attributes['last_analysis_results'] ?? [];

            $malicious = $stats['malicious'] ?? 0;
            $suspicious = $stats['suspicious'] ?? 0;
            $harmless = $stats['harmless'] ?? 0;
            $undetected = $stats['undetected'] ?? 0;

            $detections = [];
            foreach ($results as $engine => $result) {
                if (isset($result['category']) && $result['category'] === 'malicious') {
                    $detections[] = [
                        'engine' => $engine,
                        'result' => $result['result'] ?? 'Malicious',
                    ];
                }
            }

            return [
                'malicious' => $malicious,
                'suspicious' => $suspicious,
                'harmless' => $harmless,
                'undetected' => $undetected,
                'total_engines' => $malicious + $suspicious + $harmless + $undetected,
                'detections' => $detections,
                'is_safe' => $malicious === 0,
                'permalink' => $attributes['permalink'] ?? null,
                'scan_date' => $attributes['date'] ?? null,
            ];

        } catch (\Exception $e) {
            Log::error('VirusTotal check failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return ['error' => 'VirusTotal check failed: ' . $e->getMessage()];
        }
    }
}