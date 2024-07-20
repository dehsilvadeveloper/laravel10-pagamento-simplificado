<?php

namespace App\Domain\Notification\Listeners;

use Throwable;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Events\NotificationSent;

class RegisterNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        // SAVE RECORD OF SENT NOTIFICATION ON DATABASE HERE.

        Log::debug(
            '[RegisterNotification] A notification was sent.',
            [
                'recipient_id' => $event->notifiable->id,
                'type' => get_class($event->notification),
                'channel' => $event->channel,
                'notifiable' => $event->notifiable,
                'notification' => $event->notification,
                'response' => $event->response
            ]
        );
    }

    /**
     * Handle event failure.
     */
    public function failed(NotificationSent $event, Throwable $exception): void
    {
        Log::error(
            '[RegisterNotification] There was an error while trying to register a notification as sent.',
            [
                'error_message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'data' => [
                    'notification_recipient' => $event->notifiable ?? null,
                    'notification_type' => get_class($event->notification) ?? null,
                    'notification_channel' => $event->channel ?? null
                ],
                'stack_trace' => $exception->getTrace()
            ]
        );
    }
}
