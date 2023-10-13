<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CartManagerService;
use App\Interfaces\CartManagerServiceInterface;
use Illuminate\Support\ServiceProvider;
use App\Services\OrderManagerService;
use App\Interfaces\OrderManagerServiceInterface;
use \App\Services\PaypalAdapterService;
use App\Interfaces\PaypalAdapterServiceInterface;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(
            CartManagerServiceInterface::class,
            CartManagerService::class
        );

        $this->app->bind(
            OrderManagerServiceInterface::class,
            OrderManagerService::class
        );

        $this->app->bind(
            PaypalAdapterServiceInterface::class,
            PaypalAdapterService::class
        );
    }
}
