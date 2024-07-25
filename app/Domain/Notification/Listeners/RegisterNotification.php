<?php

namespace App\Domain\Notification\Listeners;

use Throwable;
use Illuminate\Mail\SentMessage;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\ValueObjects\SentSmsMessage;
use App\Domain\Notification\DataTransferObjects\CreateNotificationDto;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

class RegisterNotification
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        $this->notificationRepository->create(
            CreateNotificationDto::from([
                'recipient_id' => $event->notifiable->id,
                'type' => get_class($event->notification),
                'channel' => $event->channel,
                'response' => $this->getResponseSummary($event->response)
            ])
        );

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

    /**
     * Get the event response summary.
     */
    protected function getResponseSummary($response): string
    {
        if ($response instanceof SentMessage) {
            return 'Notification Mail sent successfully.';
        }

        if ($response instanceof SentSmsMessage) {
            return 'Notification SMS sent to ' . $response->getPhoneNumber() . ' successfully.';
        }

        return 'Notification sent successfully.';
    }
}
