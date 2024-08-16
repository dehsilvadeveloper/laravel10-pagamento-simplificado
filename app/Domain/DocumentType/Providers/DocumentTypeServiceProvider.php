<?php

namespace App\Domain\DocumentType\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\DocumentType\Services\DocumentTypeService;
use App\Domain\DocumentType\Services\Interfaces\DocumentTypeServiceInterface;

class DocumentTypeServiceProvider extends ServiceProvider
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
     * Bind service classes for domain DocumentType
     *
     * @return void
     */
    private function bindServiceClasses(): void
    {
        $this->app->bind(DocumentTypeServiceInterface::class, DocumentTypeService::class);
    }
}
