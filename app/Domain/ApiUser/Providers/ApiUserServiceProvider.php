<?php

namespace App\Domain\ApiUser\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\ApiUser\Services\ApiUserService;
use App\Domain\ApiUser\Services\Interfaces\ApiUserServiceInterface;

class ApiUserServiceProvider extends ServiceProvider
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
     * Bind service classes for domain ApiUser
     *
     * @return void
     */
    private function bindServiceClasses(): void
    {
        $this->app->bind(ApiUserServiceInterface::class, ApiUserService::class);
    }
}