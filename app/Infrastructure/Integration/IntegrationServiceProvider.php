<?php

namespace App\Infrastructure\Integration;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Integration\ExtAutho\Providers\ExtAuthoServiceProvider;
use App\Infrastructure\Integration\ExtNotifier\Providers\ExtNotifierServiceProvider;

class IntegrationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(ExtAuthoServiceProvider::class);
        $this->app->register(ExtNotifierServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
