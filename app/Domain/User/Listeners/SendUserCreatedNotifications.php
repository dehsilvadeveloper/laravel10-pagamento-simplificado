<?php

namespace App\Domain\User\Listeners;

use Throwable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Notifications\WelcomeNotification;

class SendUserCreatedNotifications implements ShouldQueue
{
    /**
     * The name of the connection the job should be sent to.
     *
     * @var string|null
     */
    public $connection = 'redis';

    /**
     * The name of the queue the job should be sent to.
     *
     * @var string|null
     */
    public $queue = 'notifications';

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UserCreated $event): void
    {
        $user = $event->user;
        $user->notify(new WelcomeNotification());
    }

    /**
     * Handle event failure.
     */
    public function failed(UserCreated $event, Throwable $exception): void
    {
        Log::error(
            '[SendUserCreatedNotifications] Failed to send notifications through the event UserCreated.',
            [
                'error_message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'data' => [
                    'user' => $event->user ?? null
                ],
                'stack_trace' => $exception->getTrace()
            ]
        );
    }
}
