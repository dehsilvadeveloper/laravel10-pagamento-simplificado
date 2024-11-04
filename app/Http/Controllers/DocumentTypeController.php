<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\DocumentType\Exceptions\DocumentTypeNotFoundException;
use App\Domain\DocumentType\Services\Interfaces\DocumentTypeServiceInterface;
use App\Http\Resources\DocumentTypeCollection;
use App\Http\Resources\DocumentTypeResource;
use App\Traits\Http\ApiResponse;

/**
 * @group Document Types
 *
 * Endpoints for managing document types
 */
class DocumentTypeController extends Controller
{
    use ApiResponse;

    public function __construct(private DocumentTypeServiceInterface $documentTypeService)
    {
    }

    /**
     * List document types
     *
     * This endpoint allows you to get a list of document types.
     *
     * @responseField id integer The identifier of the document type.
     * @responseField name string The name of the document type.
     *
     * @response status=200 scenario=success {
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "cnpj"
     *          },
     *          {
     *              "id": 2,
     *              "name": "cpf"
     *          }
     *      ]
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     *
     * @response status=500 scenario="unexpected error" {
     *      "message": "Internal Server Error."
     * }
     *
     * @authenticated
     */
    public function index(): JsonResponse
    {
        try {
            $documentTypes = $this->documentTypeService->getAll();

            return $this->sendSuccessResponse(
                data: new DocumentTypeCollection($documentTypes),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[DocumentTypeController] Failed to get list of document types.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            return $this->sendErrorResponse(
                message: 'An error has occurred. Could not get the document types list as requested.',
                code: $exception->getCode()
            );
        }
    }

    /**
     * Get a single document type
     *
     * This endpoint allows you to return a single document type from the database.
     *
     * @urlParam id integer required The identifier of the document type.
     *
     * @responseField id integer The identifier of the document type.
     * @responseField name string The name of the document type.
     *
     * @response status=200 scenario=success {
     *      "data": {
     *          "id": 2,
     *          "name": "cpf"
     *      }
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     *
     * @response status=404 scenario="Document type not found" {
     *      "message": "The document type could not be found."
     * }
     *
     * @response status=500 scenario="unexpected error" {
     *      "message": "Internal Server Error."
     * }
     *
     * @authenticated
     *
     */
    public function show(string $id): JsonResponse
    {
        try {
            $documentType = $this->documentTypeService->firstById((int) $id);

            if (!$documentType) {
                throw new DocumentTypeNotFoundException();
            }

            return $this->sendSuccessResponse(
                data: new DocumentTypeResource($documentType),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[DocumentTypeController] Failed to find the requested document type.',
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

            $exceptionTypes = [DocumentTypeNotFoundException::class];

            $errorMessage = in_array(get_class($exception), $exceptionTypes)
                ? $exception->getMessage()
                : 'An error has occurred. Could not find the document type as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }
}
