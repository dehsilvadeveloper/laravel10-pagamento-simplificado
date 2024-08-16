<?php

namespace Tests\Unit\App\Infrastructure\Integration\ExtAutho\Services;

use Tests\TestCase;
use Mockery;
use Illuminate\Http\Client\Response as ClientResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\ValueObjects\HttpRequestObject;
use App\Infrastructure\Integration\ExtAutho\Services\ExtAuthoRequestService;

class ExtAuthoRequestServiceTest extends TestCase
{
    /** @var ExtAuthoRequestService */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(ExtAuthoRequestService::class);
    }

    /**
     * @group services
     * @group ext_autho
     */
    public function test_can_send_request(): void
    {
        Http::fake();

        $endpoint = 'https://example.com/api';
        $method = HttpMethodEnum::POST;
        $body = ['key' => 'value'];
        $headers = ['Content-Type' => 'application/json'];
        $timeout = 10;

        $mockResponse = Mockery::mock(ClientResponse::class);
        $mockResponse->shouldReceive('status')->andReturn(Response::HTTP_OK);

        Http::shouldReceive('withHeaders')
            ->with($headers)
            ->andReturnSelf();

        Http::shouldReceive('timeout')
            ->with($timeout)
            ->andReturnSelf();

        Http::shouldReceive(strtolower($method->value))
            ->with($endpoint, $body)
            ->andReturn($mockResponse);

        $requestObject = new HttpRequestObject(
            $endpoint,
            $method,
            $body,
            $headers,
            $timeout
        );

        $response = $this->service->sendRequest($requestObject);

        $this->assertSame($mockResponse, $response);
        $this->assertEquals(Response::HTTP_OK, $response->status());
    }
}
