<?php

namespace App\Providers;

use App\Channels\ExtNotifierChannel;
use App\Channels\SmsChannel;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierNotificationServiceInterface;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->defineNotificationCustomChannels();
    }

    /**
     * Define custom channels for your notifications.
     */
    private function defineNotificationCustomChannels(): void
    {
        Notification::extend('sms', function ($app) {
            return new SmsChannel();
        });

        Notification::extend('ext_notifier', function ($app) {
            return new ExtNotifierChannel($app->make(ExtNotifierNotificationServiceInterface::class));
        });
    }
}
