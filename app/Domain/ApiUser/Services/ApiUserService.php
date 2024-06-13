<?php

namespace App\Domain\ApiUser\Services;

use Throwable;
use Illuminate\Support\Facades\Log;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\ApiUser\Repositories\ApiUserRepositoryInterface;
use App\Domain\ApiUser\Services\Interfaces\ApiUserServiceInterface;

class ApiUserService implements ApiUserServiceInterface
{
    public function __construct(private ApiUserRepositoryInterface $apiUserRepository)
    {
    }

    public function firstById(int $id): ?ApiUser
    {
        try {
            return $this->apiUserRepository->firstById($id);
        } catch (Throwable $exception) {
            Log::error(
                '[ApiUserService] Error while trying to find a api user with the id provided.',
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

    public function firstByEmail(string $email): ?ApiUser
    {
        try {
            return $this->apiUserRepository->firstByField('email', $email, ['id', 'name', 'email', 'password']);
        } catch (Throwable $exception) {
            Log::error(
                '[ApiUserService] Error while trying to find a api user with the email provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'email' => $email ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }
}