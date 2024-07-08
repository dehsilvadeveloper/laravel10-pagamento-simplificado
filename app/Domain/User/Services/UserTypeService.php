<?php

namespace App\Domain\User\Services;

use Throwable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Models\UserType;
use App\Domain\User\Repositories\UserTypeRepositoryInterface;
use App\Domain\User\Services\Interfaces\UserTypeServiceInterface;

class UserTypeService implements UserTypeServiceInterface
{
    public function __construct(private UserTypeRepositoryInterface $userTypeRepository)
    {
    }

    public function firstById(int $id): ?UserType
    {
        try {
            return $this->userTypeRepository->firstById($id);
        } catch (Throwable $exception) {
            Log::error(
                '[UserTypeService] Error while trying to find a user type with the id provided.',
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
            return $this->userTypeRepository->getAll($columns);
        } catch (Throwable $exception) {
            Log::error(
                '[UserTypeService] Failed to get a list of user types.',
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
