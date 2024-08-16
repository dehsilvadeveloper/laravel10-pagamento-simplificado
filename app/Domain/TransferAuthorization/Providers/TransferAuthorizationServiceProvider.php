<?php

namespace App\Domain\TransferAuthorization\Providers;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Integration\ExtAutho\Services\ExtAuthoAuthorizerService;
use App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface;

class TransferAuthorizationServiceProvider extends ServiceProvider
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
     * Bind service classes for domain Transfer Authorization
     *
     * @return void
     */
    private function bindServiceClasses(): void
    {
        $this->app->bind(TransferAuthorizerServiceInterface::class, ExtAuthoAuthorizerService::class);
    }
}
