<?php

namespace App\Domain\DocumentType\Services;

use Throwable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Domain\DocumentType\Models\DocumentType;
use App\Domain\DocumentType\Repositories\DocumentTypeRepositoryInterface;
use App\Domain\DocumentType\Services\Interfaces\DocumentTypeServiceInterface;

class DocumentTypeService implements DocumentTypeServiceInterface
{
    public function __construct(private DocumentTypeRepositoryInterface $documentTypeRepository)
    {
    }

    public function firstById(int $id): ?DocumentType
    {
        try {
            return $this->documentTypeRepository->firstById($id);
        } catch (Throwable $exception) {
            Log::error(
                '[DocumentTypeService] Error while trying to find a document type with the id provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'id' => $id ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }

    public function getAll(array $columns = ['*']): Collection
    {
        try {
            return $this->documentTypeRepository->getAll($columns);
        } catch (Throwable $exception) {
            Log::error(
                '[DocumentTypeService] Failed to get a list of document types.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }
}
