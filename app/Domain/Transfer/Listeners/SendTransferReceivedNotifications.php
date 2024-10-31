<?php

namespace App\Domain\Transfer\Listeners;

use App\Domain\Transfer\Events\TransferReceived;
use App\Domain\Transfer\Notifications\TransferReceivedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendTransferReceivedNotifications implements ShouldQueue
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
    public function handle(TransferReceived $event): void
    {
        $user = $event->transfer->payee;
        $user->notify(new TransferReceivedNotification($event->transfer));
    }

    /**
     * Handle event failure.
     */
    public function failed(TransferReceived $event, Throwable $exception): void
    {
        Log::error(
            '[SendTransferReceivedNotifications] Failed to send notifications through the event TransferReceived.',
            [
                'error_message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'data' => [
                    'event' => get_class($event),
                    'transfer' => $event->transfer ?? null
                ],
                'stack_trace' => $exception->getTrace()
            ]
        );
    }
}
