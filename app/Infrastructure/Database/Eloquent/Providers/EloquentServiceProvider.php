<?php

namespace App\Infrastructure\Database\Eloquent\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\ApiUser\Repositories\ApiUserRepositoryInterface;
use App\Domain\DocumentType\Repositories\DocumentTypeRepositoryInterface;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;
use App\Domain\Transfer\Repositories\TransferRepositoryInterface;
use App\Domain\TransferAuthorization\Repositories\TransferAuthorizationResponseRepositoryInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Repositories\UserTypeRepositoryInterface;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use App\Infrastructure\Database\Eloquent\ApiUserRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\BaseRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\DocumentTypeRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\NotificationRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\TransferRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\TransferAuthorizationResponseRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\UserRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\UserTypeRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\WalletRepositoryEloquent;
use App\Infrastructure\Database\Eloquent\Interfaces\RepositoryEloquentInterface;

class EloquentServiceProvider extends ServiceProvider
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
        $this->bindNotificationRepositoryClasses();
        $this->bindWalletRepositoryClasses();
        $this->bindTransferAuthorizationRepositoryClasses();
        $this->bindTransferRepositoryClasses();
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

    /**
     * Bind repository classes for domain Notification
     *
     * @return void
     */
    private function bindNotificationRepositoryClasses(): void
    {
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepositoryEloquent::class);
    }

    /**
     * Bind repository classes for domain Wallet
     *
     * @return void
     */
    private function bindWalletRepositoryClasses(): void
    {
        $this->app->bind(WalletRepositoryInterface::class, WalletRepositoryEloquent::class);
    }

    /**
     * Bind repository classes for domain TransferAuthorization
     *
     * @return void
     */
    private function bindTransferAuthorizationRepositoryClasses(): void
    {
        $this->app->bind(
            TransferAuthorizationResponseRepositoryInterface::class,
            TransferAuthorizationResponseRepositoryEloquent::class
        );
    }

    /**
     * Bind repository classes for domain Transfer
     *
     * @return void
     */
    private function bindTransferRepositoryClasses(): void
    {
        $this->app->bind(TransferRepositoryInterface::class, TransferRepositoryEloquent::class);
    }
}
