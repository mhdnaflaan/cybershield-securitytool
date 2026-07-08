<?php
namespace App\Services\Contracts;

interface UrlScanServiceInterface
{
    public function scan(string $url): array;
}