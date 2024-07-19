<?php

namespace App\Domain\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;

class RegisterNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
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
}
