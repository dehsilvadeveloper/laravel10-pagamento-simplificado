<?php

namespace App\Infrastructure\Database;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Database\Eloquent\BaseRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\Interfaces\RepositoryEloquentInterface;
use App\Infrastructure\Database\Eloquent\ApiUserRepositoryEloquent;
use App\Domain\ApiUser\Repositories\ApiUserRepositoryInterface;
use App\Infrastructure\Database\Eloquent\DocumentTypeRepositoryEloquent;
use App\Domain\DocumentType\Repositories\DocumentTypeRepositoryInterface;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->bindBaseRepositoryClasses();
        $this->bindApiUserRepositoryClasses();
        $this->bindDocumentTypeRepositoryClasses();
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
     * Bind base repository classes
     *
     * @return void
     */
    private function bindBaseRepositoryClasses(): void
    {
        $this->app->bind(RepositoryEloquentInterface::class, BaseRepositoryEloquent::class);
    }

    /**
     * Bind repository classes for domain ApiUser
     *
     * @return void
     */
    private function bindApiUserRepositoryClasses(): void
    {
        $this->app->bind(ApiUserRepositoryInterface::class, ApiUserRepositoryEloquent::class);
    }

    /**
     * Bind repository classes for domain DocumentType
     *
     * @return void
     */
    private function bindDocumentTypeRepositoryClasses(): void
    {
        $this->app->bind(DocumentTypeRepositoryInterface::class, DocumentTypeRepositoryEloquent::class);
    }
}