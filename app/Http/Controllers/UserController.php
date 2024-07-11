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
 * @group User
 *
 * Endpoints for managing users
 */
class UserController extends Controller
{
    use ApiResponse;

    public function __construct(private UserServiceInterface $userService)
    {
    }

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

    public function update(string $id, UpdateUserRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe()->all();

            if (empty($validatedRequest)) {
                throw new EmptyRequestException(
                    'You cannot update a resource without provide data.',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
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
}
