<?php

namespace App\Services\Scanning;

use App\Services\Contracts\UrlScanServiceInterface;
use Illuminate\Support\Facades\Http;

class UrlScanService implements UrlScanServiceInterface
{
    public function scan(string $url): array
    {
        try {
            $start = microtime(true);
            $response = Http::timeout(10)->withOptions([
                'allow_redirects' => true,
                'verify' => false,
            ])->get($url);

            $loadTime = round((microtime(true) - $start) * 1000);

            return [
                'status' => $response->successful() ? 'Online' : 'Error',
                'status_code' => $response->status(),
                'load_time_ms' => $loadTime,
                'headers' => $response->headers(),
                'is_secure' => str_starts_with($url, 'https'),
                'final_url' => $response->effectiveUrl() ?? $url,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'Offline',
                'error' => $e->getMessage(),
                'status_code' => 0,
                'load_time_ms' => 0,
                'headers' => [],
                'is_secure' => false,
                'final_url' => $url,
            ];
        }
    }
}