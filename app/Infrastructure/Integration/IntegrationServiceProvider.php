<?php

namespace App\Infrastructure\Integration;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Integration\ExtAutho\Providers\ExtAuthoServiceProvider;

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