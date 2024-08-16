<?php

namespace Tests\Unit\App\Domain\Common\ValueObjects;

use Tests\TestCase;
use App\Domain\Common\Enums\HttpMethodEnum;
use App\Domain\Common\ValueObjects\HttpRequestObject;

class HttpRequestObjectTest extends TestCase
{
    /**
     * @group value_objects
     * @group common
     */
    public function test_can_instantiate(): void
    {
        $endpoint = 'https://example.com/api';
        $method = HttpMethodEnum::POST;
        $body = ['key' => 'value'];
        $headers = ['Content-Type' => 'application/json'];
        $timeout = 10;

        $requestObject = new HttpRequestObject(
            $endpoint,
            $method,
            $body,
            $headers,
            $timeout
        );

        $this->assertEquals($endpoint, $requestObject->getEndpoint());
        $this->assertEquals(strtolower($method->value), $requestObject->getMethod());
        $this->assertEquals($body, $requestObject->getBody());
        $this->assertEquals($headers, $requestObject->getHeaders());
        $this->assertEquals($timeout, $requestObject->getTimeout());
    }

    /**
     * @group value_objects
     * @group common
     */
    public function test_can_instantiate_with_only_required_values(): void
    {
        $endpoint = 'https://example.com/api';
        $method = HttpMethodEnum::GET;

        $requestObject = new HttpRequestObject(
            $endpoint,
            $method
        );

        $this->assertEquals($endpoint, $requestObject->getEndpoint());
        $this->assertEquals(strtolower($method->value), $requestObject->getMethod());
        $this->assertEquals([], $requestObject->getBody());
        $this->assertEquals([], $requestObject->getHeaders());
        $this->assertEquals(5, $requestObject->getTimeout());
    }
}
