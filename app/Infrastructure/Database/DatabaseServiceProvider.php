<?php

namespace App\Infrastructure\Database;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Database\Eloquent\Providers\EloquentServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(EloquentServiceProvider::class);
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