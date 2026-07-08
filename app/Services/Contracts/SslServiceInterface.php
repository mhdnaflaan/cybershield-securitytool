<?php

namespace App\Services\Contracts;

use App\DTOs\SslReportDto;

interface SslServiceInterface
{
    
    
    public function check(string $domain): SslReportDto;
}