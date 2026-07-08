<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\SslServiceInterface;
use App\Services\Contracts\SslServicesInterface;
use App\Services\Contracts\UrlScanServiceInterface;
use App\Services\Contracts\UrlScanServicesInterface;
use App\Services\Scanning\SslCertificateService;
use App\Services\Scanning\UrlScanService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UrlScanServiceInterface::class,UrlScanService::class);
        $this->app->bind(SslServiceInterface::class,SslCertificateService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
