<?php

namespace App\Domain\User\Notifications;

use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;
use App\Domain\User\Mails\WelcomeMailable;

class WelcomeNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'sms'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        return (new WelcomeMailable($notifiable))->to($notifiable->email);
    }

    /**
     * Get the sms representation of the notification.
     */
    public function toSms(object $notifiable): array
    {
        return [
            'phone_number' => $notifiable->phone_number ?? '11989049461',
            'message' => "Hello, dear {$notifiable->name}. Welcome to the Simplified Payment application."
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
