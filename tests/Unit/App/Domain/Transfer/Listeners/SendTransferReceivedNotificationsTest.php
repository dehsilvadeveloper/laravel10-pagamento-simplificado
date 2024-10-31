<?php

namespace Tests\Unit\App\Domain\Transfer\Listeners;

use App\Domain\Transfer\Events\TransferReceived;
use App\Domain\Transfer\Listeners\SendTransferReceivedNotifications;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Notifications\TransferReceivedNotification;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class SendTransferReceivedNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group listeners
     * @group transfer
     */
    public function test_is_attached_to_event(): void
    {
        Event::fake();
        Event::assertListening(
            TransferReceived::class,
            SendTransferReceivedNotifications::class
        );
    }

    /**
     * @group listeners
     * @group transfer
     */
    public function test_it_send_notifications(): void
    {
        Notification::fake();
 
        $transfer = Transfer::factory()->create();

        $event = new TransferReceived($transfer);
        $listener = new SendTransferReceivedNotifications();
        $listener->handle($event);
 
        Notification::assertSentTo($transfer->payee, TransferReceivedNotification::class);
    }

    /**
     * @group listeners
     * @group transfer
     */
    public function test_it_logs_if_event_fails(): void
    {
        $transfer = Transfer::factory()->create();

        /** @var MockInterface|TransferReceived $eventMock */
        $eventMock = Mockery::mock(TransferReceived::class);
        $eventMock->transfer = $transfer;

        $fakeException = new Exception('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) use($eventMock) {
                return strpos(
                        $message,
                        '[SendTransferReceivedNotifications] Failed to send notifications through the event TransferReceived.'
                    ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false
                    && $context['data']['event'] === get_class($eventMock)
                    && $context['data']['transfer'] === $eventMock->transfer;
            });

        $listener = new SendTransferReceivedNotifications();
        $result = $listener->failed($eventMock, $fakeException);
    }
}
