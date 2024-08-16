<?php

namespace App\Infrastructure\Integration\ExtAutho\Services\Interfaces;

use Illuminate\Http\Client\Response as ClientResponse;
use App\Domain\Common\ValueObjects\HttpRequestObject;

interface ExtAuthoRequestServiceInterface
{
    public function sendRequest(HttpRequestObject $requestParams): ClientResponse;
}
