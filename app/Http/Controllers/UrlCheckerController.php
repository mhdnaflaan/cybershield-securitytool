<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Services\Helpers\UrlNormalizer;
use App\Services\ThreatIntel\GoogleSafeBrowsingService;
use App\Services\ThreatIntel\VirusTotalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class UrlCheckerController extends Controller
{
    public function index()
    {
        return view('url-checker');
    }

    public function check(
        Request $request,
        VirusTotalService $vt,
        GoogleSafeBrowsingService $gsb
    ) {
        $request->validate([
            'url' => 'required|string|max:2048',
        ]);

        // Normalize URL
        $rawUrl = $request->input('url');
        $normalizedUrl = UrlNormalizer::normalize($rawUrl);

        if (!UrlNormalizer::isValid($normalizedUrl)) {
            return back()
                ->withInput()
                ->withErrors(['url' => 'Invalid URL format.']);
        }

        Log::info('URL Check Started', [
            'raw_url' => $rawUrl,
            'normalized_url' => $normalizedUrl,
        ]);

        // Check with both services
        $vtResult = $vt->checkUrl($normalizedUrl);
        $gsbResult = $gsb->checkUrl($normalizedUrl);

        // Combine results
        $combinedResult = $this->combineResults($normalizedUrl, $vtResult, $gsbResult);

        // Save to database
        if (auth()->check()) {
            Scan::create([
                'user_id' => auth()->id(),
                'tool_name' => 'url_checker',
                'input_data' => $rawUrl,
                'result_data' => $combinedResult,
            ]);
        }

        return view('url-checker', ['result' => $combinedResult]);
    }

    /**
     * Combine VirusTotal and Google Safe Browsing results
     */
    private function combineResults(string $url, array $vt, array $gsb): array
    {
        // Determine risk level
        $isMalicious = false;
        $isSuspicious = false;
        $riskLevel = 'Low Risk';
        $riskColor = 'green';
        $detections = [];
        $messages = [];

        // Check VirusTotal
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

        // Check Google Safe Browsing
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

        // If no threats detected
        if (!$isMalicious && !$isSuspicious) {
            $messages = ['No threats detected by any security service.'];
        }

        return [
            'original_url' => $url,
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
            'cached' => false,
        ];
    }
}