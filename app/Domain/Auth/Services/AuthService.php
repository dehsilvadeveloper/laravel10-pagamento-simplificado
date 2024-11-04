<?php

namespace App\Domain\Auth\Services;

use Throwable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Domain\Auth\DataTransferObjects\ApiLoginDto;
use App\Domain\Auth\DataTransferObjects\SuccessfulAuthDto;
use App\Domain\Auth\Exceptions\IncorrectPasswordException;
use App\Domain\Auth\Exceptions\IncorrectEmailException;
use App\Domain\Auth\Services\Interfaces\AuthServiceInterface;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\ApiUser\Services\Interfaces\ApiUserServiceInterface;

class AuthService implements AuthServiceInterface
{
    public function __construct(private ApiUserServiceInterface $apiUserService)
    {
    }

    public function login(ApiLoginDto $dto): SuccessfulAuthDto
    {
        try {
            $validatedUser = $this->validateUser($dto->email, $dto->password);

            $validatedUser->tokens()->delete();

            $expiresAt = now()->addMinutes(
                env('SANCTUM_TOKEN_EXPIRATION_MINUTES', 5)
            );

            $token = $validatedUser->createToken(
                $dto->email,
                ['*'],
                $expiresAt
            )->plainTextToken;

            return SuccessfulAuthDto::from([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => $expiresAt->format('Y-m-d H:i:s')
            ]);
        } catch (Throwable $exception) {
            Log::error(
                '[AuthService] Failed to login with the credentials provided.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_dto_data' => $dto->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        }
    }

    private function validateUser(string $email, string $password): ApiUser
    {
        $user = $this->apiUserService->firstByEmail($email);

        if (!$user) {
            throw new IncorrectEmailException();
        }

        if (!Hash::check($password, $user->password)) {
            throw new IncorrectPasswordException();
        }

        return $user;
    }
}
