<?php

namespace Tests\Unit\App\Domain\Notification\Listeners;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Mail\SentMessage;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Domain\Notification\DataTransferObjects\CreateNotificationDto;
use App\Domain\Notification\Listeners\RegisterNotification;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

class RegisterNotificationTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group listeners
     * @group notification
     */
    public function test_is_attached_to_event(): void
    {
        Event::fake();
        Event::assertListening(
            NotificationSent::class,
            RegisterNotification::class
        );
    }

    /**
     * @group listeners
     * @group notification
     */
    public function test_it_register_notification_on_database(): void
    {
        /** @var MockInterface|NotificationRepositoryInterface $notificationRepositoryMock */
        $notificationRepositoryMock = Mockery::mock(NotificationRepositoryInterface::class);

        $notifiableMock = Mockery::mock();
        $notifiableMock->id = 1;

        /** @var MockInterface|NotificationSent $eventMock */
        $eventMock = Mockery::mock(NotificationSent::class);
        $eventMock->notifiable = $notifiableMock;
        $eventMock->notification = Mockery::mock();
        $eventMock->channel = 'mail';
        $eventMock->response = Mockery::mock(SentMessage::class);

        $notificationRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with(
                Mockery::on(function (CreateNotificationDto $dto) use ($eventMock) {
                    return $dto->recipientId === $eventMock->notifiable->id
                        && $dto->type === get_class($eventMock->notification)
                        && $dto->channel === $eventMock->channel
                        && $dto->response === 'Notification Mail sent successfully.';
                })
            );

        Log::shouldReceive('debug')
            ->once()
            ->with(
                '[RegisterNotification] A notification was sent.',
                [
                    'recipient_id' => $eventMock->notifiable->id,
                    'type' => get_class($eventMock->notification),
                    'channel' => $eventMock->channel,
                    'notifiable' => $eventMock->notifiable,
                    'notification' => $eventMock->notification,
                    'response' => $eventMock->response
                ]
            );

        $listener = new RegisterNotification($notificationRepositoryMock);
        $listener->handle($eventMock);

        $this->expectNotToPerformAssertions();
    }

    /**
     * @group listeners
     * @group notification
     */
    public function test_it_logs_if_event_fails(): void
    {
        /** @var MockInterface|NotificationRepositoryInterface $notificationRepositoryMock */
        $notificationRepositoryMock = Mockery::mock(NotificationRepositoryInterface::class);

        $notifiableMock = Mockery::mock();
        $notifiableMock->id = 1;

        /** @var MockInterface|NotificationSent $eventMock */
        $eventMock = Mockery::mock(NotificationSent::class);
        $eventMock->notifiable = $notifiableMock;
        $eventMock->notification = Mockery::mock();
        $eventMock->channel = 'mail';
        $eventMock->response = Mockery::mock(SentMessage::class);

        $fakeException = new Exception('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) use($eventMock) {
                return strpos(
                        $message,
                        '[RegisterNotification] There was an error while trying to register a notification as sent.'
                    ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false
                    && $context['data']['notification_recipient'] === $eventMock->notifiable
                    && $context['data']['notification_type'] === get_class($eventMock->notification)
                    && $context['data']['notification_channel'] === $eventMock->channel;
            });

        $listener = new RegisterNotification($notificationRepositoryMock);
        $listener->failed($eventMock, $fakeException);

        $this->expectNotToPerformAssertions();
    }
}
