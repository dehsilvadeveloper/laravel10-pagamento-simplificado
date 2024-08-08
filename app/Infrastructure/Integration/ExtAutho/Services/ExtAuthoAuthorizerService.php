<?php

namespace App\Infrastructure\Integration\ExtAutho\Services;

use Throwable;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\Exceptions\EmptyRequestException;
use App\Domain\Common\Exceptions\EmptyResponseException;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;
use App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;

class ExtAuthoAuthorizerService implements TransferAuthorizerServiceInterface
{
    public function __construct(private ExtAuthoRequestServiceInterface $extAuthoRequestService)
    {
    }

    public function authorize(AuthorizeTransferDto $dto): bool
    {
        try {
            $data = $dto->toArray();

            if (empty($data)) {
                return $this->handleEmptyData();
            }

            $response = $this->sendAuthorizationRequest($data);

            $this->saveAuthorizationResponseOnDatabase($dto->transferId, $response->json());

            return $this->handleResponse($response);
        } catch (Throwable $exception) {
            return $this->handleException($exception, $dto);
        }
    }

    private function handleEmptyData(): void
    {
        throw new EmptyRequestException(
            'You did not provide any data to proceed the authorization.',
            Response::HTTP_BAD_REQUEST
        );
    }

    private function sendAuthorizationRequest(array $data): ClientResponse
    {
        $request = new HttpRequestObject(
            endpoint: config('external_authorizer.urls.authorize'),
            method: HttpMethodEnum::GET,
            body: $data,
            headers: [
                'Content-Type' => 'application/json'
            ],
            timeout: 5
        );
    
        $response = $this->extAuthoRequestService->sendRequest($request);
        $response->throwUnlessStatus(Response::HTTP_FORBIDDEN);
    
        return $response;
    }

    private function saveAuthorizationResponseOnDatabase(int $transferId, array $response): void
    {
        // TODO: Inserir registro na tabela "external_authorization_responses" do banco de dados
    }

    private function handleResponse(ClientResponse $response): bool
    {
        if ($response->forbidden()) {
            return false;
        }

        $responseBody = $response->object();

        if (!$responseBody) {
            throw new EmptyResponseException();
        }

        return $responseBody->data->authorization ?? false;
    }

    private function handleException(Throwable $exception, AuthorizeTransferDto $dto): bool
    {
        Log::error(
            '[ExtAuthoAuthorizerService] Error while trying to authorize a transfer.',
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

        $this->saveAuthorizationResponseOnDatabase(
            $dto->transferId,
            [
                'exception_message' => $exception->getMessage()
            ]
        );

        return false;
    }
}
