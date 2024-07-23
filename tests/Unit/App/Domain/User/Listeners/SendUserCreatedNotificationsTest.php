<?php

namespace Tests\Unit\App\Domain\User\Listeners;

use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Listeners\SendUserCreatedNotifications;
use App\Domain\User\Models\User;
use App\Domain\User\Notifications\WelcomeNotification;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class SendUserCreatedNotificationsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group listeners
     * @group user
     */
    public function test_is_attached_to_event(): void
    {
        Event::fake();
        Event::assertListening(
            UserCreated::class,
            SendUserCreatedNotifications::class
        );
    }

    /**
     * @group listeners
     * @group user
     */
    public function test_it_send_notifications(): void
    {
        Notification::fake();
 
        $user = User::factory()->create();

        $event = new UserCreated($user);
        $listener = new SendUserCreatedNotifications();
        $listener->handle($event);
 
        Notification::assertSentTo($user, WelcomeNotification::class);
    }
}
