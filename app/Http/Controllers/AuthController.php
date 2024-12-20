<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Auth\DataTransferObjects\ApiLoginDto;
use App\Domain\Auth\Exceptions\IncorrectPasswordException;
use App\Domain\Auth\Exceptions\IncorrectEmailException;
use App\Domain\Auth\Services\Interfaces\AuthServiceInterface;
use App\Http\Requests\ApiLoginRequest;
use App\Http\Resources\AuthenticatedApiUserResource;
use App\Traits\Http\ApiResponse;

/**
 * @group Authentication
 *
 * Endpoints for managing API authentication
 */
class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private AuthServiceInterface $authService)
    {
    }

    /**
     * Login
     *
     * This endpoint lets you login an API user, generating an access token for him.
     *
     * @responseField access_token string The access token that will be used to authenticate API requests.
     * @responseField token_type string The type of token generated.
     * @responseField expires_at string The date and time in which the token will expire.
     *
     * @response status=200 scenario=success {
     *      "data": {
     *          "access_token": "1|laravel10_pagamento_simplificadoUI8A7aHrlN0XCyKApJCfO2uzK9Gc4X8DWZtFJbCY4d735783",
     *          "token_type": "Bearer",
     *          "expires_at": "2024-02-01 12:27:37"
     *      }
     * }
     *
     * @response status=400 scenario="user with email provided not found" {
     *      "message": "'Could not found a valid API user with the provided email."
     * }
     *
     * @response status=400 scenario="password incorrect" {
     *      "message": "The password provided for this API user is incorrect."
     * }
     *
     * @response status=422 scenario="validation error" {
     *      "message": "The email field is required. (and 1 more error)",
     *      "errors": {
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
     */
    public function login(ApiLoginRequest $request): JsonResponse
    {
        try {
            $dto = ApiLoginDto::from([
                'email' => $request->input('email'),
                'password' => $request->input('password')
            ]);

            $result = $this->authService->login($dto);

            return $this->sendSuccessResponse(
                data: $result->toArray(),
                code: Response::HTTP_OK
            );
        } catch (Throwable $exception) {
            Log::error(
                '[AuthController] Failed to login with the credentials provided.',
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

            $exceptionTypes = [IncorrectEmailException::class, IncorrectPasswordException::class];

            $errorMessage = in_array(get_class($exception), $exceptionTypes)
                ? $exception->getMessage()
                : 'An error has occurred. Could not login with the credentials provided as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }

    /**
     * Authenticated user
     *
     * This endpoint lets you get information about the authenticated user.
     *
     * @responseField id integer The identifier of the user.
     * @responseField name string The name of the user.
     * @responseField email string The e-mail of the user.
     * @responseField email_verified_at string The date and time in which the e-mail of the user was verified.
     * @responseField created_at string The date and time in which the user was created.
     * @responseField updated_at string The date and time in which the user was last updated.
     *
     * @response status=200 scenario=success {
     *      "data": {
     *          "id": 1,
     *          "name": "Default App",
     *          "email": "default@app.com",
     *          "email_verified_at": null,
     *          "created_at": "2024-04-02T20:06:26.000000Z",
     *          "updated_at": "2024-04-02T20:06:26.000000Z"
     *      }
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
     *
     */
    public function me(Request $request): JsonResponse
    {
        return $this->sendSuccessResponse(
            data: new AuthenticatedApiUserResource($request->user()),
            code: Response::HTTP_OK
        );
    }
}
