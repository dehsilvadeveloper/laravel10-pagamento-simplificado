<?php

namespace App\Domain;

use Illuminate\Support\ServiceProvider;
use App\Domain\ApiUser\Providers\ApiUserServiceProvider;
use App\Domain\Auth\Providers\AuthServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(ApiUserServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
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