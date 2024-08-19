<?php

namespace Tests\Unit\App\Infrastructure\Database\Eloquent;

use Tests\TestCase;
use InvalidArgumentException;
use TypeError;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Enums\TransferStatusEnum;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Infrastructure\Database\Eloquent\TransferRepositoryEloquent;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;

class TransferRepositoryEloquentTest extends TestCase
{
    /** @var TransferRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->repository = app(TransferRepositoryEloquent::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group repositories
     * @group transfer
     */
    public function test_can_create_with_integer_amount(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 500,
            'transfer_status_id' => TransferStatusEnum::PENDING->value
        ];

        $createdRecord = $this->repository->create(CreateTransferDto::from($data));

        $this->assertInstanceOf(Transfer::class, $createdRecord);
        $this->assertDatabaseHas('transfers', $data);
    }

    /**
     * @group repositories
     * @group transfer
     */
    public function test_can_create_with_float_amount(): void
    {
        $payer = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $payee = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);

        $data = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => 133.65,
            'transfer_status_id' => TransferStatusEnum::PENDING->value
        ];

        $createdRecord = $this->repository->create(CreateTransferDto::from($data));

        $this->assertInstanceOf(Transfer::class, $createdRecord);
        $this->assertDatabaseHas('transfers', $data);
    }

    /**
     * @group repositories
     * @group transfer
     */
    public function test_cannot_create_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to create the record.');

        /** @var MockInterface|CreateTransferDto $dtoMock */
        $dtoMock = Mockery::mock(CreateTransferDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->create($dtoMock);
    }

    /**
     * @group repositories
     * @group transfer
     */
    public function test_can_update_status(): void
    {
        $existingRecord = Transfer::factory()->create([
            'transfer_status_id' => TransferStatusEnum::PENDING->value
        ]);

        $updatedRecord = $this->repository->updateStatus($existingRecord->id, TransferStatusEnum::COMPLETED);

        $this->assertInstanceOf(Transfer::class, $updatedRecord);
        $this->assertEquals($existingRecord->id, $updatedRecord->id);
        $this->assertEquals(TransferStatusEnum::COMPLETED->value, $updatedRecord->transfer_status_id);
        $this->assertNotEquals(TransferStatusEnum::PENDING->value, $updatedRecord->transfer_status_id);
    }

    /**
     * @group repositories
     * @group transfer
     */
    public function test_cannot_update_with_invalid_status(): void
    {
        $this->expectException(TypeError::class);

        $existingRecord = Transfer::factory()->create([
            'transfer_status_id' => TransferStatusEnum::PENDING->value
        ]);

        /** @var TransferStatusEnum $invalidStatus */
        $invalidStatus = 'invalid_status';

        $this->repository->updateStatus($existingRecord->id, $invalidStatus);
    }

    /**
     * @group repositories
     * @group transfer
     */
    public function test_cannot_update_status_of_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->updateStatus(999, TransferStatusEnum::COMPLETED);
    }
}
