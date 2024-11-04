<?php

namespace Tests\Unit\App\Domain\Transfer\Notifications;

use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Notifications\TransferReceivedNotification;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TransferReceivedNotificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group notifications
     * @group transfer
     */
    public function test_is_sent_by_ext_notifier(): void
    {
        Notification::fake();

        $transfer = Transfer::factory()->create();
        $user = $transfer->payee;

        $user->notify(new TransferReceivedNotification($transfer));

        Notification::assertSentTo($user, TransferReceivedNotification::class, function ($notification, $channels) {
            return in_array('ext_notifier', $channels);
        });
    }
}
