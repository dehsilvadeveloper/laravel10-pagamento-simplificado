<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Exceptions\UserTypeNotFoundException;
use App\Domain\User\Services\Interfaces\UserTypeServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserTypeCollection;
use App\Http\Resources\UserTypeResource;
use App\Traits\Http\ApiResponse;

/**
 * @group User Types
 *
 * Endpoints for managing user types
 */
class UserTypeController extends Controller
{
    use ApiResponse;

    public function __construct(private UserTypeServiceInterface $userTypeService)
    {
    }

    /**
     * List user types
     *
     * This endpoint lets you get a list of user types.
     *
     * @responseField id integer The identifier of the user type.
     * @responseField name string The name of the user type.
     *
     * @response status=200 scenario=success {
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "comum"
     *          },
     *          {
     *              "id": 2,
     *              "name": "lojista"
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
            $userTypes = $this->userTypeService->getAll();

            return $this->sendSuccessResponse(
                data: new UserTypeCollection($userTypes),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserTypeController] Failed to get list of user types.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            return $this->sendErrorResponse(
                message: 'An error has occurred. Could not get the user types list as requested.',
                code: $exception->getCode()
            );
        }
    }

    /**
     * Get a single user type
     * 
     * This endpoint is used to return a single user type from the database.
     * 
     * @urlParam id integer required The identifier of the user type.
     * 
     * @responseField id integer The identifier of the user type.
     * @responseField name string The name of the user type.
     * 
     * @response status=200 scenario=success {
     *      "data": {
     *          "id": 2,
     *          "name": "comum"
     *      }
     * }
     * 
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     * 
     * @response status=404 scenario="User type not found" {
     *      "message": "The user type could not be found."
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
            $userType = $this->userTypeService->firstById((int) $id);

            if (!$userType) {
                throw new UserTypeNotFoundException();
            }

            return $this->sendSuccessResponse(
                data: new UserTypeResource($userType),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserTypeController] Failed to find the requested user type.',
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

            $exceptionTypes = [UserTypeNotFoundException::class];

            $errorMessage = in_array(get_class($exception), $exceptionTypes)
                ? $exception->getMessage()
                : 'An error has occurred. Could not find the user type as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }
}
