<?php

namespace Tests\Unit\App\Domain\User\Services;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\DataTransferObjects\CreateUserDto;
use App\Domain\User\DataTransferObjects\UpdateUserDto;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\User\Services\UserService;
use App\Domain\Wallet\DataTransferObjects\CreateWalletDto;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class UserServiceTest extends TestCase
{
    /** @var UserService */
    private $service;

    /** @var MockInterface */
    private $userRepositoryMock;

    /** @var MockInterface */
    private $walletRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->walletRepositoryMock = Mockery::mock(WalletRepositoryInterface::class);
        $this->service = app(
            UserService::class,
            [
                'userRepository' => $this->userRepositoryMock,
                'walletRepository' => $this->walletRepositoryMock
            ]
        );
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_create(): void
    {
        Event::fake();

        $userData = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12),
            'starter_balance' => fake()->randomFloat(2, 10, 900)
        ];
        $userDto = CreateUserDto::from($userData);
        $fakeUserRecord = User::factory()->make($userData);
        $fakeUserRecord->id = 10;

        $fakeWalletRecord = Wallet::factory()->for($fakeUserRecord)->make([
            'balance' => $userData['starter_balance']
        ]);

        $fakeUserRecord->setRelation('wallet', $fakeWalletRecord);

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($userDto)
            ->andReturn($fakeUserRecord);

        $this->walletRepositoryMock->shouldReceive('create')
            ->once()
            ->with(
                // Para evitar problemas com identidade de objeto, "mockamos" o DTO dentro do with()
                // Caso contrário um DTO gerado pelo teste unitário
                // e o DTO gerado dentro do service não serão considerados idênticos
                Mockery::on(function ($arg) {
                    return $arg instanceof CreateWalletDto;
                })
            )
            ->andReturn($fakeWalletRecord);

        $createdRecord = $this->service->create($userDto);

        Event::assertDispatched(UserCreated::class);

        $this->assertInstanceOf(User::class, $createdRecord);
        $this->assertEquals($fakeUserRecord->user_type_id, $createdRecord->user_type_id);
        $this->assertEquals($fakeUserRecord->name, $createdRecord->name);
        $this->assertEquals($fakeUserRecord->document_type_id, $createdRecord->document_type_id);
        $this->assertEquals($fakeUserRecord->document_number, $createdRecord->document_number);
        $this->assertEquals($fakeUserRecord->email, $createdRecord->email);
        $this->assertEquals($fakeUserRecord->password, $createdRecord->password);
        $this->assertEquals($fakeWalletRecord->balance, $createdRecord->wallet->balance);
    }

    /**
     * @group services
     * @group user
     */
    public function test_cannot_create_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        $dto = CreateUserDto::from(
            [
                'user_type_id' => UserTypeEnum::COMMON->value,
                'document_type_id' => DocumentTypeEnum::CPF->value,
                'document_number' => fake()->cpf(false),
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => fake()->password(12),
                'starter_balance' => fake()->randomFloat(2, 10, 900)
            ]
        );

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[UserService] Failed to create user with the data provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->userRepositoryMock
            ->shouldReceive('create')
            ->once()
            ->with($dto)
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->create($dto);
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_update(): void
    {
        $fakeRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ]);
        $fakeRecord->id = 1;

        $dto = UpdateUserDto::from(
            [
                'name' => 'Updated name',
                'email' => 'fake.email@test.com'
            ]
        );

        $fakeUpdatedRecord = $fakeRecord->replicate();
        $fakeUpdatedRecord->name = $dto->name;
        $fakeUpdatedRecord->email = $dto->email;

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($fakeRecord->id, $dto)
            ->andReturn($fakeUpdatedRecord);

        $updatedRecord = $this->service->update(
            $fakeRecord->id,
            $dto
        );

        $this->assertInstanceOf(User::class, $updatedRecord);
        $this->assertEquals($dto->name, $updatedRecord->name);
        $this->assertEquals($dto->email, $updatedRecord->email);
        $this->assertNotEquals($fakeRecord->name, $updatedRecord->name);
        $this->assertNotEquals($fakeRecord->email, $updatedRecord->email);
    }

    /**
     * @group services
     * @group user
     */
    public function test_cannot_update_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        $existingRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ]);
        $existingRecord->id = 1;

        $dto = UpdateUserDto::from(
            [
                'name' => 'Updated name',
                'email' => 'fake.email@test.com'
            ]
        );

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[UserService] Failed to update user with the data provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->userRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($existingRecord->id, $dto)
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->update($existingRecord->id, $dto);
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_delete(): void
    {
        $existingRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ]);
        $existingRecord->id = 1;

        $this->userRepositoryMock
            ->shouldReceive('deleteById')
            ->once()
            ->with($existingRecord->id)
            ->andReturn(true);

        $deleteResult = $this->service->delete($existingRecord->id);

        $this->assertTrue($deleteResult);
    }

    /**
     * @group services
     * @group user
     */
    public function test_cannot_delete_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        $existingRecord = User::factory()->make([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ]);
        $existingRecord->id = 1;

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[UserService] Failed to delete the user.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->userRepositoryMock
            ->shouldReceive('deleteById')
            ->once()
            ->with($existingRecord->id)
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->delete($existingRecord->id);
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_find_record_by_id(): void
    {
        $fakeRecord = User::factory()->make();
        $fakeRecord->id = 1;

        $this->userRepositoryMock
            ->shouldReceive('firstById')
            ->once()
            ->with($fakeRecord->id, ['*'])
            ->andReturn($fakeRecord);

        $foundRecord = $this->service->firstById($fakeRecord->id);

        $this->assertInstanceOf(User::class, $foundRecord);
        $this->assertEquals($fakeRecord->user_type_id, $foundRecord->user_type_id);
        $this->assertEquals($fakeRecord->name, $foundRecord->name);
        $this->assertEquals($fakeRecord->document_type_id, $foundRecord->document_type_id);
        $this->assertEquals($fakeRecord->document_number, $foundRecord->document_number);
        $this->assertEquals($fakeRecord->email, $foundRecord->email);
        $this->assertEquals($fakeRecord->password, $foundRecord->password);
    }

    /**
     * @group services
     * @group user
     */
    public function test_cannot_find_by_id_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[UserService] Error while trying to find a user with the id provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->userRepositoryMock
            ->shouldReceive('firstById')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->firstById(1);
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_find_record_by_email(): void
    {
        $fakeRecord = User::factory()->make();
        $fakeRecord->id = 1;

        $this->userRepositoryMock
            ->shouldReceive('firstByField')
            ->once()
            ->with('email', $fakeRecord->email, ['*'])
            ->andReturn($fakeRecord);

        $foundRecord = $this->service->firstByEmail($fakeRecord->email);

        $this->assertInstanceOf(User::class, $foundRecord);
        $this->assertEquals($fakeRecord->user_type_id, $foundRecord->user_type_id);
        $this->assertEquals($fakeRecord->name, $foundRecord->name);
        $this->assertEquals($fakeRecord->document_type_id, $foundRecord->document_type_id);
        $this->assertEquals($fakeRecord->document_number, $foundRecord->document_number);
        $this->assertEquals($fakeRecord->email, $foundRecord->email);
        $this->assertEquals($fakeRecord->password, $foundRecord->password);
    }

    /**
     * @group services
     * @group user
     */
    public function test_cannot_find_by_email_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[UserService] Error while trying to find a user with the email provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->userRepositoryMock
            ->shouldReceive('firstByField')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->firstByEmail('nonexistent@test.com');
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_get_list_of_records(): void
    {
        $recordsCount = 3;
        $fakeRecords = User::factory()->count($recordsCount)->make();

        $this->userRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($fakeRecords);

        $foundRecords = $this->service->getAll();

        $this->assertCount($recordsCount, $foundRecords);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($fakeRecords[$i]->user_type_id, $foundRecords[$i]->user_type_id);
            $this->assertEquals($fakeRecords[$i]->name, $foundRecords[$i]->name);
            $this->assertEquals($fakeRecords[$i]->document_type_id, $foundRecords[$i]->document_type_id);
            $this->assertEquals($fakeRecords[$i]->document_number, $foundRecords[$i]->document_number);
            $this->assertEquals($fakeRecords[$i]->email, $foundRecords[$i]->email);
            $this->assertEquals($fakeRecords[$i]->password, $foundRecords[$i]->password);
        }
    }

    /**
     * @group services
     * @group user
     */
    public function test_can_get_empty_list_of_records(): void
    {
        $this->userRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(new Collection());

        $records = $this->service->getAll();

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group services
     * @group user
     */
    public function test_cannot_get_list_of_records_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos($message, '[UserService] Failed to get a list of users.') !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->userRepositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->getAll();
    }
}
