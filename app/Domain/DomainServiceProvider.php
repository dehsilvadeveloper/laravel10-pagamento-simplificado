<?php

namespace App\Domain;

use Illuminate\Support\ServiceProvider;
use App\Domain\ApiUser\Providers\ApiUserServiceProvider;
use App\Domain\Auth\Providers\AuthServiceProvider;
use App\Domain\DocumentType\Providers\DocumentTypeServiceProvider;
use App\Domain\Transfer\Providers\TransferServiceProvider;
use App\Domain\TransferAuthorization\Providers\TransferAuthorizationServiceProvider;
use App\Domain\User\Providers\UserServiceProvider;

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
        $this->app->register(DocumentTypeServiceProvider::class);
        $this->app->register(UserServiceProvider::class);
        $this->app->register(TransferAuthorizationServiceProvider::class);
        $this->app->register(TransferServiceProvider::class);
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
