<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\CartManagerService;
use App\Interfaces\CartManagerServiceInterface;
use Illuminate\Support\ServiceProvider;

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
    }
}
