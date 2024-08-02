<?php

namespace Tests\Unit\App\Infrastructure\Database\Eloquent;

use Tests\TestCase;
use InvalidArgumentException;
use TypeError;
use Mockery;
use App\Domain\Wallet\DataTransferObjects\CreateWalletDto;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\User\Models\User;
use App\Infrastructure\Database\Eloquent\WalletRepositoryEloquent;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class WalletRepositoryEloquentTest extends TestCase
{
    /** @var WalletRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->repository = app(WalletRepositoryEloquent::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group repositories
     * @group wallet
     */
    public function test_can_create(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'balance' => fake()->randomFloat(2, 10, 900)
        ];

        $createdRecord = $this->repository->create(CreateWalletDto::from($data));

        $this->assertInstanceOf(Wallet::class, $createdRecord);
        $this->assertDatabaseHas('wallets', $data);
    }

    /**
     * @group repositories
     * @group wallet
     */
    public function test_cannot_create_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to create the record.');

        $dtoMock = Mockery::mock(CreateWalletDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->create($dtoMock);
    }

    /**
     * @group repositories
     * @group wallet
     */
    public function test_can_increment(): void
    {
        $wallet = Wallet::factory()->create([
            'balance' => 20.50
        ]);

        $incrementedRecord = $this->repository->incrementById($wallet->id, 'balance', 11.30);

        $this->assertInstanceOf(Wallet::class, $incrementedRecord);
        $this->assertNotEquals($wallet->balance, $incrementedRecord->balance);
        $this->assertEquals(31.80, $incrementedRecord->balance);
    }

    /**
     * @group repositories
     * @group wallet
     */
    public function test_cannot_increment_with_non_numeric_value(): void
    {
        $this->expectException(TypeError::class);

        $wallet = Wallet::factory()->create([
            'balance' => 20.50
        ]);

        $this->repository->incrementById($wallet->id, 'balance', 'abc');
    }

    /**
     * @group repositories
     * @group wallet
     */
    public function test_can_decrement(): void
    {
        $wallet = Wallet::factory()->create([
            'balance' => 20.50
        ]);

        $decrementedRecord = $this->repository->decrementById($wallet->id, 'balance', 1.50);

        $this->assertInstanceOf(Wallet::class, $decrementedRecord);
        $this->assertNotEquals($wallet->balance, $decrementedRecord->balance);
        $this->assertEquals(19, $decrementedRecord->balance);
    }

    /**
     * @group repositories
     * @group wallet
     */
    public function test_cannot_decrement_with_non_numeric_value(): void
    {
        $this->expectException(TypeError::class);

        $wallet = Wallet::factory()->create([
            'balance' => 20.50
        ]);

        $this->repository->decrementById($wallet->id, 'balance', 'abc');
    }
}
