<?php

namespace App\Infrastructure\Integration\ExtAutho\Services;

use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;

class ExtAuthoAuthorizerService
{
    public function __construct(private ExtAuthoRequestServiceInterface $extAuthoRequestService)
    {
    }

    public function authorize($payerId, $payeeId, $amount): bool
    {
        $response = $this->extAuthoRequestService->sendRequest(
            new HttpRequestObject(
                endpoint: 'https://example.com/api/authorize',
                method: HttpMethodEnum::POST,
                body: [],
                headers: [
                    'Content-Type' => 'application/json'
                ],
                timeout: 5
            )
        );

        return false;
    }
}
