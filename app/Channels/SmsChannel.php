<?php

namespace App\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toSms')) {
            throw new Exception('Notification is missing toSms method.');
        }

        $smsData = $notification->toSms($notifiable);

        Log::debug(
            '[SmsChannel] A SMS send was simulated.',
            [
                'phone_number' => $smsData['phone_number'],
                'message' => $smsData['message']
            ]
        );
    }
}
