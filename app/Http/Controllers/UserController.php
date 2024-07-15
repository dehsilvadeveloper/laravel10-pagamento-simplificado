<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\Exceptions\EmptyRequestException;
use App\Domain\User\DataTransferObjects\CreateUserDto;
use App\Domain\User\DataTransferObjects\UpdateUserDto;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Services\Interfaces\UserServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Traits\Http\ApiResponse;

/**
 * @group Users
 *
 * Endpoints for managing users
 */
class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private UserServiceInterface $userService)
    {
    }

    /**
     * Create user
     *
     * This endpoint lets you create a new user.
     * 
     * @responseField id integer The identifier of the user.
     * @responseField name string The name of the user.
     * @responseField user_type.id integer The identifier of the type of user.
     * @responseField user_type.name string The type of user.
     * @responseField document_type.id integer The identifier of the type of document.
     * @responseField document_type.name string The type of document.
     * @responseField document_number string The number of document.
     * @responseField email string The e-mail of the user.
     * @responseField created_at string The date and time in which the user was created.
     * @responseField updated_at string The date and time in which the user was last updated.
     * 
     * @response status=201 scenario=success {
     *      "message": "User created with success.",
     *      "data": {
     *          "id": 17,
     *          "name": "Peter Parker",
     *          "user_type": {
     *              "id": 1,
     *              "name": "comum"
     *          },
     *          "document_type": {
     *              "id": 2,
     *              "name": "cpf"
     *          },
     *          "document_number": "06633022000",
     *          "email": "peter.parker@marvel.com",
     *          "created_at": "2024-07-12 15:42:18",
     *          "updated_at": "2024-07-12 15:42:18"
     *      }
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     * 
     * @response status=422 scenario="validation error" {
     *      "message": "The user type id field is required. (and 5 more errors)",
     *      "errors": {
     *          "user_type_id": [
     *              "The user type id field is required."
     *          ],
     *          "name": [
     *              "The name field is required."
     *          ],
     *          "document_type_id": [
     *              "The document type id field is required."
     *          ],
     *          "document_number": [
     *              "The document number field is required."
     *          ],
     *          "email": [
     *              "The email field is required."
     *          ],
     *          "password": [
     *              "The password field is required."
     *          ]
     *      }
     * }
     *
     * @response status=500 scenario="unexpected error" {
     *      "message": "Internal Server Error."
     * }
     *
     * @authenticated
     * 
     */
    public function create(CreateUserRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe()->all();
            $newUser = $this->userService->create(CreateUserDto::from($validatedRequest));

            return $this->sendSuccessResponse(
                message: 'User created with success.',
                data: new UserResource($newUser),
                code: Response::HTTP_CREATED
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserController] Failed to create a new user.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_data' => $request->all() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            return $this->sendErrorResponse(
                message: 'An error has occurred. Could not create the new user as requested.',
                code: $exception->getCode()
            );
        }
    }

    /**
     * Update user
     *
     * This endpoint lets you update a user.
     * 
     * @urlParam id integer required The identifier of the user.
     * 
     * @responseField id integer The identifier of the user.
     * @responseField name string The name of the user.
     * @responseField user_type.id integer The identifier of the type of user.
     * @responseField user_type.name string The type of user.
     * @responseField document_type.id integer The identifier of the type of document.
     * @responseField document_type.name string The type of document.
     * @responseField document_number string The number of document.
     * @responseField email string The e-mail of the user.
     * @responseField created_at string The date and time in which the user was created.
     * @responseField updated_at string The date and time in which the user was last updated.
     * 
     * @response status=200 scenario=success {
     *      "message": "User updated with success.",
     *      "data": {
     *          "id": 17,
     *          "name": "Peter Parker",
     *          "user_type": {
     *              "id": 1,
     *              "name": "comum"
     *          },
     *          "document_type": {
     *              "id": 2,
     *              "name": "cpf"
     *          },
     *          "document_number": "06633022000",
     *          "email": "peter.parker@marvel.com",
     *          "created_at": "2024-07-12 15:42:18",
     *          "updated_at": "2024-07-12 15:42:18"
     *      }
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     * 
     * @response status=404 scenario="User not found" {
     *      "message": "The user could not be found."
     * }
     * 
     * @response status=422 scenario="validation error" {
     *      "message": "You cannot update a resource without provide data."
     * }
     * 
     * @response status=422 scenario="validation error" {
     *      "message": "The email field must be a valid email address.",
     *      "errors": {
     *          "email": [
     *              "The email field must be a valid email address."
     *          ]
     *      }
     * }
     *
     * @response status=500 scenario="unexpected error" {
     *      "message": "Internal Server Error."
     * }
     *
     * @authenticated
     * 
     */
    public function update(string $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe()->all();

            if (empty($validatedRequest)) {
                throw new EmptyRequestException();
            }

            $updatedUser = $this->userService->update((int) $id, UpdateUserDto::from($validatedRequest));

            return $this->sendSuccessResponse(
                message: 'User updated with success.',
                data: new UserResource($updatedUser),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserController] Failed to update the user.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'id' => $id ?? null,
                        'received_data' => $request->all() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            $exceptionTypes = [EmptyRequestException::class, UserNotFoundException::class];

            $errorMessage = in_array(get_class($exception), $exceptionTypes)
                ? $exception->getMessage()
                : 'An error has occurred. Could not update the user as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }

    /**
     * Delete user
     *
     * This endpoint lets you delete a user. This API uses a soft delete approach, so the user will still exists in the database, but will be marked as deleted.
     * 
     * @urlParam id integer required The identifier of the user.
     * 
     * @response status=200 scenario=success {
     *      "message": "User deleted with success."
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     * 
     * @response status=404 scenario="User not found" {
     *      "message": "The user could not be found."
     * }
     *
     * @response status=500 scenario="unexpected error" {
     *      "message": "Internal Server Error."
     * }
     *
     * @authenticated
     * 
     */
    public function delete(string $id): JsonResponse
    {
        try {
            $this->userService->delete((int) $id);

            return $this->sendSuccessResponse(
                message: 'User deleted with success.',
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserController] Failed to delete the user.',
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

            $exceptionTypes = [UserNotFoundException::class];

            $errorMessage = in_array(get_class($exception), $exceptionTypes)
                ? $exception->getMessage()
                : 'An error has occurred. Could not delete the user as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }
}
