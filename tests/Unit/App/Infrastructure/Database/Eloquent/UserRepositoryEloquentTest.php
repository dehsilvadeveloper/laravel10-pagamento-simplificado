<?php

namespace Tests\Unit\App\Infrastructure\Database\Eloquent;

use Tests\TestCase;
use InvalidArgumentException;
use Mockery;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\DataTransferObjects\CreateUserDto;
use App\Domain\User\DataTransferObjects\UpdateUserDto;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Infrastructure\Database\Eloquent\UserRepositoryEloquent;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class UserRepositoryEloquentTest extends TestCase
{
    /** @var UserRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);

        $this->repository = app(UserRepositoryEloquent::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_create(): void
    {
        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];

        $createdRecord = $this->repository->create(
            CreateUserDto::from($data)
        );

        $this->assertInstanceOf(User::class, $createdRecord);
        $this->assertDatabaseHas('users', Arr::except($data, ['password']));
        $this->assertTrue(Hash::check($data['password'], $createdRecord->password));
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_create_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to create the record.');

        $dtoMock = Mockery::mock(CreateUserDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->create($dtoMock);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_update(): void
    {
        $existingRecord = User::factory()->create();

        $dataForUpdate = [
            'name' => 'Updated name',
            'document_number' => fake()->cpf(false)
        ];

        $updatedRecord = $this->repository->update(
            $existingRecord->id,
            UpdateUserDto::from($dataForUpdate)
        );

        $this->assertInstanceOf(User::class, $updatedRecord);
        $this->assertEquals($existingRecord->id, $updatedRecord->id);
        $this->assertEquals($dataForUpdate['name'], $updatedRecord->name);
        $this->assertEquals($dataForUpdate['document_number'], $updatedRecord->document_number);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_update_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to update the record.');

        $existingRecord = User::factory()->create();

        $dtoMock = Mockery::mock(UpdateUserDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->update($existingRecord->id, $dtoMock);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_update_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->update(
            1,
            UpdateUserDto::from([
                'name' => 'Updated name',
                'document_number' => fake()->cpf(false)
            ])
        );
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_delete_by_id(): void
    {
        $existingRecordData = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];
        $existingRecord = User::factory()->create($existingRecordData);

        $deleteResult = $this->repository->deleteById($existingRecord->id);

        $this->assertTrue($deleteResult);
        $this->assertDatabaseMissing('users', $existingRecordData);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_delete_by_id_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->deleteById(1);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_get_list_of_records(): void
    {
        $recordsCount = 3;

        $generatedRecords = User::factory()->count($recordsCount)->create();
        $generatedRecordsAsArray = $generatedRecords->toArray();

        $records = $this->repository->getAll();
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['user_type_id'], $recordsAsArray[$i]['user_type_id']);
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
            $this->assertEquals($generatedRecordsAsArray[$i]['document_type_id'], $recordsAsArray[$i]['document_type_id']);
            $this->assertEquals($generatedRecordsAsArray[$i]['document_number'], $recordsAsArray[$i]['document_number']);
            $this->assertEquals($generatedRecordsAsArray[$i]['email'], $recordsAsArray[$i]['email']);
        }
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_get_empty_list_of_records(): void
    {
        $records = $this->repository->getAll();

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_get_list_filtered_by_field(): void
    {
        $recordsCount = 3;

        $generatedRecords = User::factory()->count($recordsCount)->create([
            'user_type_id' => UserTypeEnum::COMMON->value
        ]);
        $generatedRecordsAsArray = $generatedRecords->toArray();

        $records = $this->repository->getByField('user_type_id', UserTypeEnum::COMMON->value);
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['user_type_id'], $recordsAsArray[$i]['user_type_id']);
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
            $this->assertEquals($generatedRecordsAsArray[$i]['document_type_id'], $recordsAsArray[$i]['document_type_id']);
            $this->assertEquals($generatedRecordsAsArray[$i]['document_number'], $recordsAsArray[$i]['document_number']);
            $this->assertEquals($generatedRecordsAsArray[$i]['email'], $recordsAsArray[$i]['email']);
        }
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_get_empty_list_filtered_by_field(): void
    {
        $records = $this->repository->getByField('user_type_id', 9999);

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_find_by_id(): void
    {
        $existingRecord = User::factory()->create();

        $foundRecord = $this->repository->firstById($existingRecord->id);

        $this->assertInstanceOf(User::class, $foundRecord);
        $this->assertEquals($existingRecord->user_type_id, $foundRecord->user_type_id);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
        $this->assertEquals($existingRecord->document_type_id, $foundRecord->document_type_id);
        $this->assertEquals($existingRecord->document_number, $foundRecord->document_number);
        $this->assertEquals($existingRecord->email, $foundRecord->email);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_find_by_id_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstById(1);

        $this->assertNull($foundRecord);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_find_by_field(): void
    {
        $fakeEmail = fake()->unique()->safeEmail();
        $existingRecord = User::factory()->create([
            'email' => $fakeEmail
        ]);

        $foundRecord = $this->repository->firstByField('email', $fakeEmail);

        $this->assertInstanceOf(User::class, $foundRecord);
        $this->assertEquals($existingRecord->user_type_id, $foundRecord->user_type_id);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
        $this->assertEquals($existingRecord->document_type_id, $foundRecord->document_type_id);
        $this->assertEquals($existingRecord->document_number, $foundRecord->document_number);
        $this->assertEquals($existingRecord->email, $foundRecord->email);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_find_by_field_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstByField('email', 'nonexistent.email@test.com');

        $this->assertNull($foundRecord);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_can_find_where(): void
    {
        $existingRecordData = [
            'email' => fake()->unique()->safeEmail()
        ];

        $existingRecord = User::factory()->create([
            'email' => $existingRecordData['email']
        ]);

        $foundRecord = $this->repository->firstWhere([
            'email' => $existingRecordData['email']
        ]);

        $this->assertInstanceOf(User::class, $foundRecord);
        $this->assertEquals($existingRecord->user_type_id, $foundRecord->user_type_id);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
        $this->assertEquals($existingRecord->document_type_id, $foundRecord->document_type_id);
        $this->assertEquals($existingRecord->document_number, $foundRecord->document_number);
        $this->assertEquals($existingRecord->email, $foundRecord->email);
    }

    /**
     * @group repositories
     * @group user
     */
    public function test_cannot_find_where_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstWhere([
            'email' => 'nonexistent.email@test.com'
        ]);

        $this->assertNull($foundRecord);
    }
}
