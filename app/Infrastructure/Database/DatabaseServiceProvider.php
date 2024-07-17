<?php

namespace App\Infrastructure\Database;

use Illuminate\Support\ServiceProvider;
use App\Infrastructure\Database\Eloquent\BaseRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\Interfaces\RepositoryEloquentInterface;
use App\Infrastructure\Database\Eloquent\ApiUserRepositoryEloquent;
use App\Domain\ApiUser\Repositories\ApiUserRepositoryInterface;
use App\Infrastructure\Database\Eloquent\DocumentTypeRepositoryEloquent;
use App\Domain\DocumentType\Repositories\DocumentTypeRepositoryInterface;
use App\Infrastructure\Database\Eloquent\UserTypeRepositoryEloquent;
use App\Domain\User\Repositories\UserTypeRepositoryInterface;
use App\Infrastructure\Database\Eloquent\UserRepositoryEloquent;
use App\Domain\User\Repositories\UserRepositoryInterface;

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
        $this->bindUserRepositoryClasses();
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

    /**
     * Bind repository classes for domain User
     *
     * @return void
     */
    private function bindUserRepositoryClasses(): void
    {
        $this->app->bind(UserTypeRepositoryInterface::class, UserTypeRepositoryEloquent::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepositoryEloquent::class);
    }
}