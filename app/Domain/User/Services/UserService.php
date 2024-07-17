<?php

namespace App\Domain\User\Services;

use Throwable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\User\DataTransferObjects\CreateUserDto;
use App\Domain\User\DataTransferObjects\UpdateUserDto;
use App\Domain\User\Exceptions\UserNotFoundException;
use App\Domain\User\Models\User;
use App\Domain\User\Services\Interfaces\UserServiceInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService implements UserServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function create(CreateUserDto $userDto): ?User
    {
        DB::beginTransaction();

        try {
            $user = $this->userRepository->create($userDto);

            DB::commit();

            return $user;
        } catch (Throwable $exception) {
            DB::rollBack();

            Log::error(
                '[UserService] Failed to create user with the data provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_user_dto' => $userDto->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }

    public function update(int $id, UpdateUserDto $dto): User
    {
        try {
            return $this->userRepository->update($id, $dto);
        } catch (ModelNotFoundException $exception) {
            Log::error(
                '[UserService] Cannot update. The user could not be found.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'id' => $id ?? null,
                        'received_dto' => $dto->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw new UserNotFoundException();
        } catch (Throwable $exception) {
            Log::error(
                '[UserService] Failed to update user with the data provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'id' => $id ?? null,
                        'received_dto' => $dto->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }

    public function delete(int $id): bool
    {
        try {
            return $this->userRepository->deleteById($id);
        } catch (ModelNotFoundException $exception) {
            Log::error(
                '[UserService] Cannot delete. The user could not be found.',
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

            throw new UserNotFoundException();
        } catch (Throwable $exception) {
            Log::error(
                '[UserService] Failed to delete the user.',
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

    public function firstById(int $id, array $columns = ['*']): ?User
    {
        try {
            return $this->userRepository->firstById($id, $columns);
        } catch (Throwable $exception) {
            Log::error(
                '[UserService] Error while trying to find a user with the id provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'id' => $id ?? null,
                        'columns' => $columns ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }

    public function firstByEmail(string $email, array $columns = ['*']): ?User
    {
        try {
            return $this->userRepository->firstByField('email', $email, $columns);
        } catch (Throwable $exception) {
            Log::error(
                '[UserService] Error while trying to find a user with the email provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'email' => $email ?? null,
                        'columns' => $columns ?? null
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
            return $this->userRepository->getAll($columns);
        } catch (Throwable $exception) {
            Log::error(
                '[UserService] Failed to get a list of users.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'columns' => $columns ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }
}
