<?php

namespace App\Channels;

use App\Infrastructure\Integration\ExtNotifier\DataTransferObjects\SendNotificationDto;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierNotificationServiceInterface;
use App\Infrastructure\Integration\ExtNotifier\ValueObjects\SentExtNotifierMessageObject;
use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ExtNotifierChannel
{
    public function __construct(
        private ExtNotifierNotificationServiceInterface $extNotifierService
    ) {
    }

    public function send(object $notifiable, Notification $notification): SentExtNotifierMessageObject
    {
        if (!method_exists($notification, 'toExtNotifier')) {
            throw new Exception('Notification is missing toExtNotifier method.');
        }

        $data = $notification->toExtNotifier($notifiable);
        ['recipient' => $recipient, 'message' => $message] = $data;

        $this->extNotifierService->notify(
            SendNotificationDto::from([
                'recipient' => $recipient,
                'message' => $message
            ])
        );

        Log::debug(
            '[ExtNotifierChannel] A Ext Notifier send was simulated.',
            [
                'recipient' => $recipient,
                'message' => $message
            ]
        );

        return new SentExtNotifierMessageObject($recipient, $message, 'sent');
    }
}
