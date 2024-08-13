<?php

namespace Tests\Unit\App\Infrastructure\Database\Eloquent;

use Tests\TestCase;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\TransferAuthorization\DataTransferObjects\CreateTransferAuthorizationResponseDto;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;
use App\Infrastructure\Database\Eloquent\TransferAuthorizationResponseRepositoryEloquent;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;

class TransferAuthorizationResponseRepositoryEloquentTest extends TestCase
{
    /** @var TransferAuthorizationResponseRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->repository = app(TransferAuthorizationResponseRepositoryEloquent::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group repositories
     * @group transfer_authorization
     */
    public function test_can_create(): void
    {
        $transfer = Transfer::factory()->create();

        $data = [
            'transfer_id' => $transfer->id,
            'response' => json_encode([
                'status' => 'success',
                'authorization' => true
            ])
        ];

        $createdRecord = $this->repository->create(CreateTransferAuthorizationResponseDto::from($data));

        $this->assertInstanceOf(TransferAuthorizationResponse::class, $createdRecord);
        $this->assertDatabaseHas('transfer_authorization_responses', $data);
    }

    /**
     * @group repositories
     * @group transfer_authorization
     */
    public function test_cannot_create_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to create the record.');

        /** @var MockInterface|CreateTransferAuthorizationResponseDto $dtoMock */
        $dtoMock = Mockery::mock(CreateTransferAuthorizationResponseDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->create($dtoMock);
    }
}
