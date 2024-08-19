<?php

namespace App\Domain\Transfer\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Transfer\Services\TransferService;
use App\Domain\Transfer\Services\Interfaces\TransferServiceInterface;

class TransferServiceProvider extends ServiceProvider
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
     * Bind service classes for domain Transfer
     *
     * @return void
     */
    private function bindServiceClasses(): void
    {
        $this->app->bind(TransferServiceInterface::class, TransferService::class);
    }
}
