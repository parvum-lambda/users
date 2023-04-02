<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ReflectionException;
use Support\CQRS\CQRSService;

class CQRSServiceProvider extends ServiceProvider
{
    /**
     * @return void
     *
     * @throws ReflectionException
     */
    public function register() : void
    {
        $CQRSService = new CQRSService();
        $this->app->singleton(CQRSService::class, function () use ($CQRSService) {
            return $CQRSService;
        });

        $CQRSService->init();
    }
}
