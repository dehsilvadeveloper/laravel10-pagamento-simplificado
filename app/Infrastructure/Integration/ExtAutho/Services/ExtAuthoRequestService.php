<?php

namespace App\Infrastructure\Integration\ExtAutho\Services;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Http;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Infrastructure\Integration\ExtAutho\Services\Interfaces\ExtAuthoRequestServiceInterface;

class ExtAuthoRequestService implements ExtAuthoRequestServiceInterface
{
    public function sendRequest(HttpRequestObject $requestParams): ClientResponse
    {
        return Http::withHeaders($requestParams->getHeaders())
            ->timeout($requestParams->getTimeout())
            ->{$requestParams->getMethod()}($requestParams->getEndpoint(), $requestParams->getBody());
    }
}
