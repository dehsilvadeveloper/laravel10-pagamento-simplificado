<?php

namespace Tests\Unit\App\Domain\User\Listeners;

use App\Domain\User\Events\UserCreated;
use App\Domain\User\Listeners\SendUserCreatedNotifications;
use App\Domain\User\Models\User;
use App\Domain\User\Notifications\WelcomeNotification;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;
use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

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

    /**
     * @group listeners
     * @group user
     */
    public function test_it_logs_if_event_fails(): void
    {
        $user = User::factory()->create();

        /** @var MockInterface|UserCreated $eventMock */
        $eventMock = Mockery::mock(UserCreated::class);
        $eventMock->user = $user;

        $fakeException = new Exception('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) use($eventMock) {
                return strpos(
                        $message,
                        '[SendUserCreatedNotifications] Failed to send notifications through the event UserCreated.'
                    ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false
                    && $context['data']['event'] === get_class($eventMock)
                    && $context['data']['user'] === $eventMock->user;
            });

        $listener = new SendUserCreatedNotifications();
        $result = $listener->failed($eventMock, $fakeException);

        $this->assertTrue(empty($result));
    }
}
