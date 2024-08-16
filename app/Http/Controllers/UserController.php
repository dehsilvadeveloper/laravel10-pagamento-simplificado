<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
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
use App\Http\Resources\UserCollection;
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
     * This endpoint allows you to create a new user.
     * 
     * @responseField id integer The identifier of the user.
     * @responseField name string The name of the user.
     * @responseField user_type.id integer The identifier of the type of user.
     * @responseField user_type.name string The type of user.
     * @responseField document_type.id integer The identifier of the type of document.
     * @responseField document_type.name string The type of document.
     * @responseField document_number string The number of document.
     * @responseField email string The e-mail of the user.
     * @responseField wallet.id integer The identifier of the wallet of the user.
     * @responseField wallet.balance float The current balance of the wallet of the user.
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
     *          "wallet": {
     *              "id": 1,
     *              "balance": 150.10
     *          }
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
     *          ],
     *          "starter_balance": [
     *              "The starter balance field is required."
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
     * This endpoint allows you to update a user.
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
     * @responseField wallet.id integer The identifier of the wallet of the user.
     * @responseField wallet.balance float The current balance of the wallet of the user.
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
     *          "wallet": {
     *              "id": 1,
     *              "balance": 150.10
     *          }
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
     *      "message": "You cannot process a resource without provide data."
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
     * This endpoint allows you to delete a user. This API uses a soft delete approach, so the user will still exists in the database, but will be marked as deleted.
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

    /**
     * Get a single user
     *
     * This endpoint allows you to return a single user from the database.
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
     * @responseField wallet.id integer The identifier of the wallet of the user.
     * @responseField wallet.balance float The current balance of the wallet of the user.
     * @responseField created_at string The date and time in which the user was created.
     * @responseField updated_at string The date and time in which the user was last updated.
     * 
     * @response status=200 scenario=success {
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
     *          "wallet": {
     *              "id": 1,
     *              "balance": 150.10
     *          }
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
            $user = $this->userService->firstById((int) $id);

            if (!$user) {
                throw new UserNotFoundException();
            }

            return $this->sendSuccessResponse(
                data: new UserResource($user),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserController] Failed to find the user.',
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
                : 'An error has occurred. Could not find the user as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }

    /**
     * List users
     *
     * This endpoint allows you to get a list of users.
     *
     * @responseField id integer The identifier of the user.
     * @responseField name string The name of the user.
     * @responseField user_type.id integer The identifier of the type of user.
     * @responseField user_type.name string The type of user.
     * @responseField document_type.id integer The identifier of the type of document.
     * @responseField document_type.name string The type of document.
     * @responseField document_number string The number of document.
     * @responseField email string The e-mail of the user.
     * @responseField wallet.id integer The identifier of the wallet of the user.
     * @responseField wallet.balance float The current balance of the wallet of the user.
     * @responseField created_at string The date and time in which the user was created.
     * @responseField updated_at string The date and time in which the user was last updated.
     *
     * @response status=200 scenario=success {
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "John Doe",
     *              "user_type": {
     *                  "id": 1,
     *                  "name": "comum"
     *              },
     *              "document_type": {
     *                  "id": 2,
     *                  "name": "cpf"
     *              },
     *              "document_number": "70349142069",
     *              "email": "john.doe@test.com",
     *              "wallet": {
     *                  "id": 1,
     *                  "balance": 150.10
     *              }
     *              "created_at": "2024-07-11 15:24:09",
     *              "updated_at": "2024-07-11 17:01:20"
     *          },
     *          {
     *              "id": 2,
     *              "name": "Stark Industries",
     *              "user_type": {
     *                  "id": 2,
     *                  "name": "lojista"
     *              },
     *              "document_type": {
     *                  "id": 1,
     *                  "name": "cnpj"
     *              },
     *              "document_number": "04716808000120",
     *              "email": "stark.industries@fake.com",
     *              "wallet": {
     *                  "id": 1,
     *                  "balance": 150.10
     *              }
     *              "created_at": "2024-07-11 15:24:10",
     *              "updated_at": "2024-07-11 15:24:10"
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
            $users = $this->userService->getAll();

            return $this->sendSuccessResponse(
                data: new UserCollection($users),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[UserController] Failed to get list of users.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'stack_trace' => $exception->getTrace()
                ]
            );

            return $this->sendErrorResponse(
                message: 'An error has occurred. Could not get the users list as requested.',
                code: $exception->getCode()
            );
        }
    }
}
