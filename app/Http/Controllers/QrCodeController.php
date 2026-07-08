<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\Helpers\UrlNormalizer;
use App\Services\ThreatIntel\VirusTotalService;
use App\Services\ThreatIntel\GoogleSafeBrowsingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    public function index()
    {
        return view('qr-checker');
    }

    public function check(Request $request, VirusTotalService $vt, GoogleSafeBrowsingService $gsb)
    {
        $request->validate([
            'qr_data' => 'nullable|string',
            'qr_image' => 'nullable|image|max:2048',
        ]);

        $decodedText = null;
        $inputType = 'text';
        $decodingMethod = 'text';

        // If image uploaded
        if ($request->hasFile('qr_image')) {
            $image = $request->file('qr_image');

            // Try local GD decoding first
            $decodedText = $this->decodeWithGD($image);

            if ($decodedText) {
                $decodingMethod = 'gd_local';
                $inputType = 'image';
            } else {
                // Fallback to API
                $decodedText = $this->decodeWithApi($image);
                if ($decodedText) {
                    $decodingMethod = 'api_fallback';
                    $inputType = 'image';
                } else {
                    return back()
                        ->withInput()
                        ->withErrors(['qr_image' => 'Could not decode QR code using either method. Please try another image or paste the URL manually.']);
                }
            }
        } else {
            // Use pasted text
            $decodedText = $request->input('qr_data');
            if (empty($decodedText)) {
                return back()->withErrors(['qr_data' => 'Please paste QR content or upload an image.']);
            }
        }

        // Validate URL
        if (!filter_var($decodedText, FILTER_VALIDATE_URL)) {
            return view('qr-checker', [
                'error' => 'The QR code does not contain a valid URL.',
                'decoded_text' => $decodedText,
            ]);
        }

        // Normalize URL
        $normalizedUrl = UrlNormalizer::normalize($decodedText);

        // Check with VirusTotal
        $vtResult = $vt->checkUrl($normalizedUrl);

        // Check with Google Safe Browsing
        $gsbResult = $gsb->checkUrl($normalizedUrl);

        // Combine results
        $result = $this->combineResults($normalizedUrl, $vtResult, $gsbResult);
        $result['input_type'] = $inputType;
        $result['decoding_method'] = $decodingMethod;
        $result['decoded_text'] = $decodedText;

        // Save to database
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'qr_checker',
                'input_data' => $decodedText,
                'result_data' => $result,
            ]);
        }

        return view('qr-checker', [
            'result' => $result,
            'decoded_text' => $decodedText,
            'input_type' => $inputType,
            'decoding_method' => $decodingMethod,
        ]);
    }

    /**
     * Try to decode QR using local GD extension (fast, private)
     */
    private function decodeWithGD($image)
    {
        // Check if GD is available
        if (!extension_loaded('gd')) {
            Log::info('GD extension not loaded, skipping local QR decoding');
            return null;
        }

        // Check if QR reader class exists
        if (!class_exists('Zxing\QrReader')) {
            Log::info('Zxing\QrReader class not found, skipping local QR decoding');
            return null;
        }

        try {
            $qrReader = new \Zxing\QrReader($image->getPathname(), \Zxing\QrReader::SOURCE_TYPE_FILE);
            $text = $qrReader->text();
           
            if (!empty($text)) {
                Log::info('Local QR decoding succeeded', ['text' => $text]);
                return $text;
            }
           
            Log::info('Local QR decoding returned empty result');
            return null;

        } catch (\Exception $e) {
            Log::warning('Local QR decoding failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Decode QR using external API (fallback)
     */
    private function decodeWithApi($image)
    {
        try {
            $response = Http::attach(
                'file', file_get_contents($image->getPathname()), 'qr.png'
            )->timeout(10)->post('https://api.qrserver.com/v1/read-qr-code/');

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data) && isset($data[0]['symbol'][0]['data'])) {
                    $text = $data[0]['symbol'][0]['data'];
                    Log::info('API QR decoding succeeded', ['text' => $text]);
                    return $text;
                }
            }

            Log::warning('API QR decoding failed', ['status' => $response->status()]);
            return null;

        } catch (\Exception $e) {
            Log::error('API QR decoding error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function combineResults(string $url, array $vt, array $gsb): array
    {
        $isMalicious = false;
        $isSuspicious = false;
        $riskLevel = 'Low Risk';
        $riskColor = 'green';
        $detections = [];
        $messages = [];

        // Check VirusTotal
        if (isset($vt['error'])) {
            $messages[] = 'VirusTotal: ' . $vt['error'];
        } else {
            if (isset($vt['malicious']) && $vt['malicious'] > 0) {
                $isMalicious = true;
                $riskLevel = 'High Risk';
                $riskColor = 'red';
                $messages[] = 'VirusTotal flagged ' . $vt['malicious'] . ' engines as malicious.';
                $detections = array_merge($detections, $vt['detections'] ?? []);
            }

            if (isset($vt['suspicious']) && $vt['suspicious'] > 0 && !$isMalicious) {
                $isSuspicious = true;
                $riskLevel = 'Medium Risk';
                $riskColor = 'yellow';
                $messages[] = 'VirusTotal flagged ' . $vt['suspicious'] . ' engines as suspicious.';
            }
        }

        // Check Google Safe Browsing
        if (isset($gsb['error'])) {
            $messages[] = 'Google Safe Browsing: ' . $gsb['error'];
        } else {
            if (isset($gsb['is_safe']) && !$gsb['is_safe']) {
                $isMalicious = true;
                $riskLevel = 'High Risk';
                $riskColor = 'red';
                $messages[] = $gsb['message'] ?? 'Google Safe Browsing flagged this URL.';
                $detections[] = [
                    'engine' => 'Google Safe Browsing',
                    'result' => implode(', ', array_column($gsb['threats'] ?? [], 'threat_type')),
                ];
            }
        }

        if (!$isMalicious && !$isSuspicious && empty($messages)) {
            $messages = ['✅ No threats detected. The QR code appears safe.'];
        }

        return [
            'url' => $url,
            'normalized_url' => $url,
            'risk_level' => $riskLevel,
            'risk_color' => $riskColor,
            'is_malicious' => $isMalicious,
            'is_suspicious' => $isSuspicious,
            'detections' => $detections,
            'messages' => $messages,
            'virustotal' => $vt,
            'google_safe_browsing' => $gsb,
            'checked_at' => now()->toDateTimeString(),
        ];
    }
}