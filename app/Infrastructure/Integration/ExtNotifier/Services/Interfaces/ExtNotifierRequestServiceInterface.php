<?php

namespace App\Infrastructure\Integration\ExtNotifier\Services\Interfaces;

use Illuminate\Http\Client\Response as ClientResponse;
use App\Domain\Common\ValueObjects\HttpRequestObject;

interface ExtNotifierRequestServiceInterface
{
    public function sendRequest(HttpRequestObject $requestParams): ClientResponse;
}
