<?php

namespace App\Services\ThreatIntel;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleSafeBrowsingService
{
    /**
     * Check URL with Google Safe Browsing API
     */
    public function checkUrl(string $url): array
    {
        $apiKey = config('services.google_safe_browsing.api_key');

        if (empty($apiKey)) {
            return ['error' => 'Google Safe Browsing API key not configured'];
        }

        $cacheKey = 'gsb_' . md5($url);

        return Cache::remember($cacheKey, 1800, function () use ($url, $apiKey) {
            return $this->performCheck($url, $apiKey);
        });
    }

    /**
     * Perform the actual API call
     */
    private function performCheck(string $url, string $apiKey): array
    {
        try {
            $response = Http::post(
                "https://safebrowsing.googleapis.com/v4/threatMatches:find?key={$apiKey}",
                [
                    'client' => [
                        'clientId' => 'cybershield-lk',
                        'clientVersion' => '1.0.0',
                    ],
                    'threatInfo' => [
                        'threatTypes' => [
                            'MALWARE',
                            'SOCIAL_ENGINEERING',
                            'UNWANTED_SOFTWARE',
                            'POTENTIALLY_HARMFUL_APPLICATION',
                        ],
                        'platformTypes' => ['ANY_PLATFORM'],
                        'threatEntryTypes' => ['URL'],
                        'threatEntries' => [
                            ['url' => $url],
                        ],
                    ],
                ]
            );

            if ($response->failed()) {
                Log::warning('Google Safe Browsing API failed', [
                    'url' => $url,
                    'status' => $response->status(),
                ]);
                return ['error' => 'API request failed'];
            }

            $data = $response->json();

            // If no matches, URL is safe
            if (!isset($data['matches']) || empty($data['matches'])) {
                return [
                    'is_safe' => true,
                    'threats' => [],
                    'message' => 'No threats detected by Google Safe Browsing.',
                ];
            }

            // Parse threats
            $threats = [];
            foreach ($data['matches'] as $match) {
                $threats[] = [
                    'threat_type' => $match['threatType'] ?? 'Unknown',
                    'platform' => $match['platformType'] ?? 'Unknown',
                    'cache_duration' => $match['cacheDuration'] ?? 'N/A',
                ];
            }

            return [
                'is_safe' => false,
                'threats' => $threats,
                'message' => 'URL flagged by Google Safe Browsing: ' . implode(', ', array_column($threats, 'threat_type')),
            ];

        } catch (\Exception $e) {
            Log::error('Google Safe Browsing check failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return ['error' => 'Check failed: ' . $e->getMessage()];
        }
    }
}