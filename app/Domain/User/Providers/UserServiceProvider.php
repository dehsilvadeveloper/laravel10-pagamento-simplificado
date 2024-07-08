<?php

namespace App\Domain\User\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\User\Services\UserTypeService;
use App\Domain\User\Services\Interfaces\UserTypeServiceInterface;

class UserServiceProvider extends ServiceProvider
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
     * Bind repository classes for domain User
     *
     * @return void
     */
    private function bindServiceClasses(): void
    {
        $this->app->bind(UserTypeServiceInterface::class, UserTypeService::class);
    }
}
