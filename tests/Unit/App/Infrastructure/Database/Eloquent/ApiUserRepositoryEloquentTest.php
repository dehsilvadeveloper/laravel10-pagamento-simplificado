<?php

namespace Tests\Unit\App\Domain\ApiUser\Repositories;

use Tests\TestCase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use App\Domain\ApiUser\Models\ApiUser;
use App\Infrastructure\Database\Eloquent\ApiUserRepositoryEloquent;

class ApiUserRepositoryEloquentTest extends TestCase
{
    /** @var ApiUserRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ApiUserRepositoryEloquent::class);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_create(): void
    {
        $data = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password'
        ];

        $createdRecord = $this->repository->create($data);

        $this->assertInstanceOf(ApiUser::class, $createdRecord);
        $this->assertDatabaseHas('users', $data);
        $this->assertTrue(Hash::check('password', $createdRecord->password));
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_update(): void
    {
        $existingRecord = ApiUser::factory()->create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password'
        ]);

        $dataForUpdate = [
            'name' => 'Updated name',
            'email' => fake()->unique()->safeEmail(),
        ];

        $updatedRecord = $this->repository->update($existingRecord->id, $dataForUpdate);

        $this->assertInstanceOf(ApiUser::class, $updatedRecord);
        $this->assertEquals($existingRecord->id, $updatedRecord->id);
        $this->assertEquals($dataForUpdate['name'], $updatedRecord->name);
        $this->assertEquals($dataForUpdate['email'], $updatedRecord->email);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_cannot_update_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $dataForUpdate = [
            'name' => 'Updated name',
            'email' => fake()->unique()->safeEmail(),
        ];

        $this->repository->update(1, $dataForUpdate);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_delete_by_id(): void
    {
        $existingRecordData = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password'
        ];
        $existingRecord = ApiUser::factory()->create($existingRecordData);

        $deleteResult = $this->repository->deleteById($existingRecord->id);

        $this->assertTrue($deleteResult);
        $this->assertDatabaseMissing('users', $existingRecordData);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_cannot_delete_by_id_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->deleteById(1);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_get_list_of_records(): void
    {
        $recordsCount = 3;

        $generatedRecords = ApiUser::factory()->count($recordsCount)->create();
        $generatedRecordsAsArray = $generatedRecords->toArray();

        $records = $this->repository->getAll();
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
            $this->assertEquals($generatedRecordsAsArray[$i]['email'], $recordsAsArray[$i]['email']);
        }
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_get_empty_list_of_records(): void
    {
        $records = $this->repository->getAll();

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_get_list_filtered_by_field(): void
    {
        $recordsCount = 3;
        $fakeName = fake()->name();

        $generatedRecords = ApiUser::factory()->count($recordsCount)->create([
            'name' => $fakeName
        ]);
        $generatedRecordsAsArray = $generatedRecords->toArray();

        $records = $this->repository->getByField('name', $fakeName);
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
            $this->assertEquals($generatedRecordsAsArray[$i]['email'], $recordsAsArray[$i]['email']);
        }
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_get_empty_list_filtered_by_field(): void
    {
        $records = $this->repository->getByField('name', 'nonexistent name');

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_find_by_id(): void
    {
        $existingRecord = ApiUser::factory()->create();

        $foundRecord = $this->repository->firstById($existingRecord->id);

        $this->assertInstanceOf(ApiUser::class, $foundRecord);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
        $this->assertEquals($existingRecord->email, $foundRecord->email);
        $this->assertEquals($existingRecord->password, $foundRecord->password);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_cannot_find_by_id_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstById(1);

        $this->assertNull($foundRecord);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_find_by_field(): void
    {
        $fakeName = fake()->name();
        $existingRecord = ApiUser::factory()->create([
            'name' => $fakeName
        ]);

        $foundRecord = $this->repository->firstByField('name', $fakeName);

        $this->assertInstanceOf(ApiUser::class, $foundRecord);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
        $this->assertEquals($existingRecord->email, $foundRecord->email);
        $this->assertEquals($existingRecord->password, $foundRecord->password);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_cannot_find_by_field_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstByField('name', 'nonexistent name');

        $this->assertNull($foundRecord);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_can_find_where(): void
    {
        $existingRecordData = [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail()
        ];

        $existingRecord = ApiUser::factory()->create([
            'name' => $existingRecordData['name'],
            'email' => $existingRecordData['email']
        ]);

        $foundRecord = $this->repository->firstWhere([
            'name' => $existingRecordData['name'],
            'email' => $existingRecordData['email']
        ]);

        $this->assertInstanceOf(ApiUser::class, $foundRecord);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
        $this->assertEquals($existingRecord->email, $foundRecord->email);
        $this->assertEquals($existingRecord->password, $foundRecord->password);
    }

    /**
     * @group repositories
     * @group api_user
     */
    public function test_cannot_find_where_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstWhere([
            'name' => 'Special',
            'email' => 'nonexistent@test.com'
        ]);

        $this->assertNull($foundRecord);
    }
}