<?php

namespace App\Infrastructure\Integration\ExtAutho\Services;

use Throwable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\Exceptions\EmptyRequestException;
use App\Domain\Common\Exceptions\EmptyResponseException;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;
use App\Domain\TransferAuthorization\DataTransferObjects\CreateTransferAuthorizationResponseDto;
use App\Domain\TransferAuthorization\Repositories\TransferAuthorizationResponseRepositoryInterface;
use App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;

class ExtAuthoAuthorizerService implements TransferAuthorizerServiceInterface
{
    public function __construct(
        private ExtAuthoRequestServiceInterface $extAuthoRequestService,
        private TransferAuthorizationResponseRepositoryInterface $transferAuthorizationResponseRepository
    ) {
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
        } catch (EmptyRequestException $exception) {
            return $this->handleEmptyRequestException($exception, $dto);
        } catch (ConnectionException|RequestException $exception) {
            return $this->handleClientException($exception, $dto);
        } catch (EmptyResponseException $exception) {
            return $this->handleClientEmptyResponseException($exception, $dto);
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
            timeout: 3
        );
    
        $response = $this->extAuthoRequestService->sendRequest($request);
        $response->throwUnlessStatus(Response::HTTP_FORBIDDEN);
    
        return $response;
    }

    private function saveAuthorizationResponseOnDatabase(int $transferId, array $response): void
    {
        $this->transferAuthorizationResponseRepository->create(
            CreateTransferAuthorizationResponseDto::from([
                'transfer_id' => $transferId,
                'response' => json_encode($response)
            ])
        );
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

    private function handleEmptyRequestException(EmptyRequestException $exception,  AuthorizeTransferDto $dto): bool
    {
        $this->writeErrorLog(
            '[ExtAuthoAuthorizerService] No data was provided for the authorization process.',
            $exception,
            [
                'received_dto_data' => $dto->toArray() ?? null
            ]
        );

        return false;
    }

    private function handleClientException(HttpClientException $exception, AuthorizeTransferDto $dto): bool
    {
        $this->writeErrorLog(
            '[ExtAuthoAuthorizerService] The authorizer returned an error.',
            $exception,
            [
                'received_dto_data' => $dto->toArray() ?? null
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

    private function handleClientEmptyResponseException(
        EmptyResponseException $exception,
        AuthorizeTransferDto $dto
    ): bool {
        $this->writeErrorLog(
            '[ExtAuthoAuthorizerService] The authorizer returned a empty response.',
            $exception,
            [
                'received_dto_data' => $dto->toArray() ?? null
            ]
        );

        return false;
    }

    private function handleException(Throwable $exception, AuthorizeTransferDto $dto): bool
    {
        $this->writeErrorLog(
            '[ExtAuthoAuthorizerService] Error while trying to authorize a transfer.',
            $exception,
            [
                'received_dto_data' => $dto->toArray() ?? null
            ]
        );

        return false;
    }

    private function writeErrorLog(string $message, Throwable $exception, array $additionalData = []): void
    {
        $context = [
            'error_message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine()
        ];

        if (!empty($extraData)) {
            $context['data'] = $additionalData;
        }

        $context['stack_trace'] = $exception->getTrace();

        Log::error($message, $context);
    }
}
