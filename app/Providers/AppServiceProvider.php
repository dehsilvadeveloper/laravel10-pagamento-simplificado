<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use App\Channels\SmsChannel;

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
    }
}
