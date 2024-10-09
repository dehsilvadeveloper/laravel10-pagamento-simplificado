<?php

namespace App\Infrastructure\Integration\ExtNotifier\Providers;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Integration\ExtNotifier\Services\ExtNotifierRequestService;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierRequestServiceInterface;
use App\Infrastructure\Integration\ExtNotifier\Services\ExtNotifierNotificationService;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierNotificationServiceInterface;

class ExtNotifierServiceProvider extends ServiceProvider
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
        $this->app->bind(ExtNotifierRequestServiceInterface::class, ExtNotifierRequestService::class);
        $this->app->bind(ExtNotifierNotificationServiceInterface::class, ExtNotifierNotificationService::class);
    }
}
