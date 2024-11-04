<?php

namespace Tests\Unit\App\Domain\DocumentType\Services;

use Tests\TestCase;
use Exception;
use Mockery;
use Mockery\MockInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Domain\DocumentType\Models\DocumentType;
use App\Domain\DocumentType\Repositories\DocumentTypeRepositoryInterface;
use App\Domain\DocumentType\Services\DocumentTypeService;

class DocumentTypeServiceTest extends TestCase
{
    /** @var DocumentTypeService */
    private $service;

    /** @var MockInterface */
    private $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = Mockery::mock(DocumentTypeRepositoryInterface::class);
        $this->service = app(DocumentTypeService::class, ['documentTypeRepository' => $this->repositoryMock]);
    }

    /**
     * @group services
     * @group document_type
     */
    public function test_can_find_record_by_id(): void
    {
        $generatedRecord = DocumentType::factory()->make();
        $generatedRecord->id = 1;

        $this->repositoryMock
            ->shouldReceive('firstById')
            ->once()
            ->with($generatedRecord->id)
            ->andReturn($generatedRecord);

        $foundRecord = $this->service->firstById($generatedRecord->id);

        $this->assertInstanceOf(DocumentType::class, $foundRecord);
        $this->assertEquals($generatedRecord->name, $foundRecord->name);
    }

    /**
     * @group services
     * @group document_type
     */
    public function test_cannot_find_by_id_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos(
                    $message,
                    '[DocumentTypeService] Error while trying to find a document type with the id provided.'
                ) !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->repositoryMock
            ->shouldReceive('firstById')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->firstById(1);
    }

    /**
     * @group services
     * @group document_type
     */
    public function test_can_get_list_of_records(): void
    {
        $recordsCount = 3;
        $generatedRecords = DocumentType::factory()->count($recordsCount)->make();
        $generatedRecordsAsArray = $generatedRecords->toArray();

        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($generatedRecords);

        $records = $this->service->getAll();
        $recordsAsArray = $records->toArray();

        $this->assertCount($recordsCount, $records);

        for ($i = 0; $i <= ($recordsCount - 1); $i++) {
            $this->assertEquals($generatedRecordsAsArray[$i]['name'], $recordsAsArray[$i]['name']);
        }
    }

    /**
     * @group services
     * @group document_type
     */
    public function test_can_get_empty_list_of_records(): void
    {
        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn(new Collection());

        $records = $this->service->getAll();

        $this->assertCount(0, $records);
        $this->assertTrue($records->isEmpty());
    }

    /**
     * @group services
     * @group document_type
     */
    public function test_cannot_get_list_of_records_if_exception_occurs(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Houston, we have a problem.');

        Log::shouldReceive('error')
            ->withArgs(function ($message, $context) {
                return strpos($message, '[DocumentTypeService] Failed to get a list of document types.') !== false
                    && strpos($context['error_message'], 'Houston, we have a problem.') !== false;
            });

        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andThrows(new Exception('Houston, we have a problem.'));

        $this->service->getAll();
    }
}
