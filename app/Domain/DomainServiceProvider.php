<?php

namespace App\Domain;

use Illuminate\Support\ServiceProvider;
use App\Domain\ApiUser\Providers\ApiUserServiceProvider;

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