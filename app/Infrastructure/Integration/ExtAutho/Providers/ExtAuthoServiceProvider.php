<?php

namespace App\Infrastructure\Integration\Providers;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Integration\ExtAutho\Services\ExtAuthoRequestService;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;

class ExtAuthoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindServiceClasses();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Bind service classes
     *
     * @return void
     */
    private function bindServiceClasses(): void
    {
        $this->app->bind(ExtAuthoRequestServiceInterface::class, ExtAuthoRequestService::class);
    }
}
