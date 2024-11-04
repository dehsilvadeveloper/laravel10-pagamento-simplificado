<?php

namespace App\Infrastructure\Integration\ExtNotifier\Services;

use Throwable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\Exceptions\EmptyRequestException;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Infrastructure\Integration\ExtNotifier\DataTransferObjects\SendNotificationDto;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierNotificationServiceInterface;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierRequestServiceInterface;

class ExtNotifierNotificationService implements ExtNotifierNotificationServiceInterface
{
    public function __construct(
        private ExtNotifierRequestServiceInterface $extNotifierRequestService
    ) {
    }

    public function notify(SendNotificationDto $dto): ClientResponse
    {
        try {
            $data = $dto->toArray();

            if (empty($data)) {
                throw new EmptyRequestException(
                    'You did not provide any data to proceed the notification.',
                    Response::HTTP_BAD_REQUEST
                );
            }

            return $this->sendNotificationRequest($data);
        } catch (Throwable $exception) {
            $errorSummary = $this->getErrorSummary($exception);

            $this->writeErrorLog(
                '[ExtNotifierNotificationService] ' . $errorSummary,
                $exception,
                [
                    'received_dto_data' => $dto->toArray() ?? null
                ]
            );

            throw $exception;
        }
    }

    private function sendNotificationRequest(array $data): ClientResponse
    {
        $request = new HttpRequestObject(
            endpoint: config('external_notifier.urls.notify'),
            method: HttpMethodEnum::POST,
            body: $data,
            headers: [
                'Content-Type' => 'application/json'
            ],
            timeout: 3
        );

        $response = $this->extNotifierRequestService->sendRequest($request);
        $response->throw();

        return $response;
    }

    private function getErrorSummary(Throwable $exception): string
    {
        return match (get_class($exception)) {
            EmptyRequestException::class => $exception->getMessage(),
            ConnectionException::class => 'The notifier returned an error.',
            RequestException::class => 'The notifier returned an error.',
            default => 'Error while trying to send a notification.'
        };
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
