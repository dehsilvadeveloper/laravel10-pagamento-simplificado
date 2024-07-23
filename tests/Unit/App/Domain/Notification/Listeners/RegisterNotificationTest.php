<?php

namespace Tests\Unit\App\Domain\Notification\Listeners;

use Tests\TestCase;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Domain\Notification\Listeners\RegisterNotification;

class RegisterNotificationTest extends TestCase
{
    /**
     * @group notifications
     * @group user
     */
    public function test_is_attached_to_event(): void
    {
        Event::fake();
        Event::assertListening(
            NotificationSent::class,
            RegisterNotification::class
        );
    }
}
