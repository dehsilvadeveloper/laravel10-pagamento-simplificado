<?php

namespace App\Infrastructure\Integration\ExtNotifier\Providers;

use Illuminate\Support\ServiceProvider;

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
        // TODO: Include binds for service classes here
    }
}
