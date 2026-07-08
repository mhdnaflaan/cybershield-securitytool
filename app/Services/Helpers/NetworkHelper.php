<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class NetworkHelper
{
    /**
     * Check if the system has internet connectivity
     */
    public static function hasInternet($timeout = 5): bool
    {
        try {
            $response = Http::timeout($timeout)->get('https://www.google.com');
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a specific API is reachable
     */
    public static function isApiReachable($url, $timeout = 5): bool
    {
        try {
            $response = Http::timeout($timeout)->get($url);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}