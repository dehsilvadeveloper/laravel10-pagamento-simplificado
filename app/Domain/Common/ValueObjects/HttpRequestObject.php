<?php

namespace App\Domain\Common\ValueObjects;

use App\Domain\Common\Enums\HttpMethodEnum;

final class HttpRequestObject
{
    private string $endpoint;
    private HttpMethodEnum $method;
    private array $body;
    private array $headers;
    private int $timeout;

    public function __construct(
        string $endpoint,
        HttpMethodEnum $method,
        array $body = [],
        array $headers = [],
        int $timeout = 5
    ) {
        $this->endpoint = $endpoint;
        $this->method = $method;
        $this->body = $body;
        $this->headers = $headers;
        $this->timeout = $timeout;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getMethod(): string
    {
        return strtolower($this->method->value);
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
