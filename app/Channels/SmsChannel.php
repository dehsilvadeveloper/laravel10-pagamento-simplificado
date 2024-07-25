<?php

namespace App\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\ValueObjects\SentSmsMessage;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): SentSmsMessage
    {
        if (!method_exists($notification, 'toSms')) {
            throw new Exception('Notification is missing toSms method.');
        }

        $response = $notification->toSms($notifiable);

        Log::debug(
            '[SmsChannel] A SMS send was simulated.',
            [
                'phone_number' => $response->getPhoneNumber(),
                'status' => $response->getStatus(),
                'message' => $response->getMessage()
            ]
        );

        return $response;
    }
}
