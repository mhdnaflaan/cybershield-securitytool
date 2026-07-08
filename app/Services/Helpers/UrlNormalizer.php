<?php

namespace App\Services\Helpers;

class UrlNormalizer
{
    /**
     * Normalize URL: add protocol, remove trailing slashes
     */
    public static function normalize(string $url): string
    {
        $url = trim($url);

        // Add https:// if no protocol
        if (!preg_match('#^https?://#', $url)) {
            $url = 'https://' . $url;
        }

        // Remove trailing slashes
        $url = rtrim($url, '/');

        return $url;
    }

    /**
     * Check if URL is valid
     */
    public static function isValid(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    
     //Extract domain from URL
     
    public static function extractDomain(string $url): string
    {
        $parsed = parse_url($url);
        return $parsed['host'] ?? $url;
    }
}