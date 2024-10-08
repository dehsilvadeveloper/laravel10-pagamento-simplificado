<?php

namespace App\Infrastructure\Integration\ExtNotifier\Services;

use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Support\Facades\Http;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Infrastructure\Integration\ExtNotifier\Services\Interfaces\ExtNotifierRequestServiceInterface;

class ExtNotifierRequestService implements ExtNotifierRequestServiceInterface
{
    public function sendRequest(HttpRequestObject $requestParams): ClientResponse
    {
        return Http::withHeaders($requestParams->getHeaders())
            ->timeout($requestParams->getTimeout())
            ->{$requestParams->getMethod()}($requestParams->getEndpoint(), $requestParams->getBody());
    }
}
