<?php

namespace Tests\Unit\App\Infrastructure\Database\Eloquent;

use Tests\TestCase;
use InvalidArgumentException;
use Mockery;
use App\Domain\Notification\DataTransferObjects\CreateNotificationDto;
use App\Domain\Notification\Models\Notification;
use App\Domain\User\Models\User;
use App\Infrastructure\Database\Eloquent\NotificationRepositoryEloquent;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class NotificationRepositoryEloquentTest extends TestCase
{
    /** @var NotificationRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->repository = app(NotificationRepositoryEloquent::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group repositories
     * @group notification
     */
    public function test_can_create(): void
    {
        $user = User::factory()->create();

        $data = [
            'recipient_id' => $user->id,
            'type' => 'App\\Domain\\User\\Notifications\\WelcomeNotification',
            'channel' => 'mail',
            'response' => null
        ];

        $createdRecord = $this->repository->create(CreateNotificationDto::from($data));

        $this->assertInstanceOf(Notification::class, $createdRecord);
        $this->assertDatabaseHas('notifications', $data);
    }

    /**
     * @group repositories
     * @group notification
     */
    public function test_cannot_create_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to create the record.');

        $dtoMock = Mockery::mock(CreateNotificationDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->create($dtoMock);
    }
}
