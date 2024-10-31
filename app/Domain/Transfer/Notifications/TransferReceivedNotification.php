<?php

namespace App\Domain\Transfer\Notifications;

use App\Domain\Transfer\Models\Transfer;
use Illuminate\Notifications\Notification;

class TransferReceivedNotification extends Notification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(protected Transfer $transfer)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['ext_notifier'];
    }

    /**
     * Get the ext_notifier representation of the notification.
     */
    public function toExtNotifier(object $notifiable): array
    {
        return [
            'recipient' => $notifiable->email,
            'message' => sprintf(
                config('transfer.notification_messages.transfer_received'),
                $this->transfer->payer->name,
                $this->transfer->amount
            )
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
