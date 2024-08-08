<?php

namespace App\Infrastructure\Integration\ExtAutho\Services;

use Throwable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\Exceptions\EmptyRequestException;
use App\Domain\Common\Exceptions\EmptyResponseException;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;

class ExtAuthoAuthorizerService
{
    public function __construct(private ExtAuthoRequestServiceInterface $extAuthoRequestService)
    {
    }

    public function authorize(AuthorizeTransferDto $dto)
    {
        try {
            $data = $dto->toArray();

            if (empty($data)) {
                throw new EmptyRequestException(
                    'You did not provide any data to proceed the authorization.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            $response = $this->extAuthoRequestService->sendRequest(
                new HttpRequestObject(
                    endpoint: config('external_authorizer.urls.authorize'),
                    method: HttpMethodEnum::POST,
                    body: $data,
                    headers: [
                        'Content-Type' => 'application/json'
                    ],
                    timeout: 5
                )
            );
            $response->throw();

            // TODO: Inserir registro na tabela "external_authorization_responses" do banco de dados usando $response->json()

            $responseBody = $response->object();

            if (!$responseBody) {
                throw new EmptyResponseException();
            }

            return (bool) $responseBody->authorization;
        } catch (Throwable $exception) {
            Log::error(
                '[ExtAuthoAuthorizerService] Error while trying to authorize a transfer with the data provided.',
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

            // TODO: Inserir registro na tabela "external_authorization_responses" do banco de dados

            return false;
        }
    }
}
