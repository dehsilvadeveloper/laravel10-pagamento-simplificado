<?php

namespace Tests\Unit\App\Infrastructure\Database\Eloquent;

use Tests\TestCase;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Domain\DocumentType\DataTransferObjects\CreateDocumentTypeDto;
use App\Domain\DocumentType\DataTransferObjects\UpdateDocumentTypeDto;
use App\Domain\DocumentType\Models\DocumentType;
use App\Infrastructure\Database\Eloquent\DocumentTypeRepositoryEloquent;

class DocumentTypeRepositoryEloquentTest extends TestCase
{
    /** @var DocumentTypeRepositoryEloquent */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(DocumentTypeRepositoryEloquent::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_create(): void
    {
        $data = [
            'name' => fake()->name()
        ];

        $createdRecord = $this->repository->create(
            CreateDocumentTypeDto::from([
                'name' => $data['name']
            ])
        );

        $this->assertInstanceOf(DocumentType::class, $createdRecord);
        $this->assertDatabaseHas('document_types', $data);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_create_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to create the record.');

        /** @var MockInterface|CreateDocumentTypeDto $dtoMock */
        $dtoMock = Mockery::mock(CreateDocumentTypeDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->create($dtoMock);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_update(): void
    {
        $existingRecord = DocumentType::factory()->create([
            'name' => fake()->name()
        ]);

        $dataForUpdate = [
            'name' => 'Updated name',
        ];

        $updatedRecord = $this->repository->update(
            $existingRecord->id,
            UpdateDocumentTypeDto::from([
                'name' => $dataForUpdate['name']
            ])
        );

        $this->assertInstanceOf(DocumentType::class, $updatedRecord);
        $this->assertEquals($existingRecord->id, $updatedRecord->id);
        $this->assertEquals($dataForUpdate['name'], $updatedRecord->name);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_update_without_data(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('You did not provide any data to update the record.');

        $existingRecord = DocumentType::factory()->create([
            'name' => fake()->name()
        ]);

        /** @var MockInterface|UpdateDocumentTypeDto $dtoMock */
        $dtoMock = Mockery::mock(UpdateDocumentTypeDto::class);
        $dtoMock->shouldReceive('toArray')->andReturn([]);

        $this->repository->update($existingRecord->id, $dtoMock);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_update_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->update(
            1,
            UpdateDocumentTypeDto::from([
                'name' => 'Updated name'
            ])
        );
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_delete_by_id(): void
    {
        $existingRecordData = [
            'name' => fake()->name()
        ];
        $existingRecord = DocumentType::factory()->create($existingRecordData);

        $deleteResult = $this->repository->deleteById($existingRecord->id);

        $this->assertTrue($deleteResult);
        $this->assertDatabaseMissing('document_types', $existingRecordData);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_delete_by_id_a_nonexistent_record(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->deleteById(1);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_get_list_of_records(): void
    {
        $recordsCount = 3;

        $generatedRecords = DocumentType::factory()->count($recordsCount)->create();
        $sortedGeneratedRecords = $generatedRecords->sortByDesc('id');
        $sortedGeneratedRecords = $sortedGeneratedRecords->values();
        $generatedRecordsAsArray = $sortedGeneratedRecords->toArray();

        $records = $this->repository->getAll();
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
        }
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_get_empty_list_of_records(): void
    {
        $records = $this->repository->getAll();

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_get_list_filtered_by_field(): void
    {
        $recordsCount = 3;
        $fakeName = fake()->name();

        $generatedRecords = DocumentType::factory()->count($recordsCount)->create([
            'name' => $fakeName
        ]);
        $generatedRecordsAsArray = $generatedRecords->toArray();

        $records = $this->repository->getByField('name', $fakeName);
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
        }
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_get_empty_list_filtered_by_field(): void
    {
        $records = $this->repository->getByField('name', 'nonexistent name');

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_find_by_id(): void
    {
        $existingRecord = DocumentType::factory()->create();

        $foundRecord = $this->repository->firstById($existingRecord->id);

        $this->assertInstanceOf(DocumentType::class, $foundRecord);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_find_by_id_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstById(1);

        $this->assertNull($foundRecord);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_find_by_field(): void
    {
        $fakeName = fake()->name();
        $existingRecord = DocumentType::factory()->create([
            'name' => $fakeName
        ]);

        $foundRecord = $this->repository->firstByField('name', $fakeName);

        $this->assertInstanceOf(DocumentType::class, $foundRecord);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_find_by_field_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstByField('name', 'nonexistent name');

        $this->assertNull($foundRecord);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_can_find_where(): void
    {
        $existingRecordData = [
            'name' => fake()->name()
        ];

        $existingRecord = DocumentType::factory()->create([
            'name' => $existingRecordData['name']
        ]);

        $foundRecord = $this->repository->firstWhere([
            'name' => $existingRecordData['name']
        ]);

        $this->assertInstanceOf(DocumentType::class, $foundRecord);
        $this->assertEquals($existingRecord->name, $foundRecord->name);
    }

    /**
     * @group repositories
     * @group document_type
     */
    public function test_cannot_find_where_a_nonexistent_record(): void
    {
        $foundRecord = $this->repository->firstWhere([
            'name' => 'Special'
        ]);

        $this->assertNull($foundRecord);
    }
}
