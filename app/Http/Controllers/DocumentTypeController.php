<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\DocumentType\Exceptions\DocumentTypeNotFoundException;
use App\Domain\DocumentType\Services\Interfaces\DocumentTypeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentTypeCollection;
use App\Http\Resources\DocumentTypeResource;
use App\Traits\Http\ApiResponse;

/**
 * @group Document Type
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
     * This endpoint lets you get a list of document types.
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

            return (new DocumentTypeCollection($documentTypes))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
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
     * This endpoint is used to return a single document type from the database.
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
                throw new DocumentTypeNotFoundException(
                    'The document type could not be found.',
                    Response::HTTP_NOT_FOUND
                );
            }

            return (new DocumentTypeResource($documentType))
                ->response()
                ->setStatusCode(Response::HTTP_OK);
        } catch (DocumentTypeNotFoundException $exception) {
            return $this->sendErrorResponse(
                message: $exception->getMessage(),
                code: $exception->getCode()
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

            return $this->sendErrorResponse(
                message: 'An error has occurred. Could not find the document type as requested.',
                code: $exception->getCode()
            );
        }
    }
}