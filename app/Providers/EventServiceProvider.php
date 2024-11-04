<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Notifications\Events\NotificationSent;
use App\Domain\Notification\Listeners\RegisterNotification;
use App\Domain\Transfer\Events\TransferReceived;
use App\Domain\Transfer\Listeners\SendTransferReceivedNotifications;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Listeners\SendUserCreatedNotifications;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class
        ],
        NotificationSent::class => [
            RegisterNotification::class
        ],
        UserCreated::class => [
            SendUserCreatedNotifications::class
        ],
        TransferReceived::class => [
            SendTransferReceivedNotifications::class
        ]
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
