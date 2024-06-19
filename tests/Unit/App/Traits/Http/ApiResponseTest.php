<?php

namespace Tests\Unit\App\Traits\Http;

use Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Traits\Http\ApiResponse;

class ApiResponseTest extends TestCase
{
    use ApiResponse;

    /**
     * @group traits
     * @group api_response
     */
    public function test_can_send_success_response(): void
    {
        $message = 'Success message';
        $data = ['key' => 'value'];
        $code = Response::HTTP_OK;

        $response = $this->sendSuccessResponse($message, $data, $code);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($code, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => $message, 'data' => $data]),
            $response->getContent()
        );
    }

    /**
     * @group traits
     * @group api_response
     */
    public function test_can_send_error_response(): void
    {
        $message = 'Error message';
        $data = ['error' => 'details'];
        $code = Response::HTTP_BAD_REQUEST;

        $response = $this->sendErrorResponse($message, $data, $code);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($code, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => $message, 'data' => $data]),
            $response->getContent()
        );
    }

    /**
     * @group traits
     * @group api_response
     */
    public function test_can_send_default_success_response(): void
    {
        $response = $this->sendSuccessResponse();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode([]),
            $response->getContent()
        );
    }

    /**
     * @group traits
     * @group api_response
     */
    public function test_can_send_default_error_response(): void
    {
        $message = 'Error message';

        $response = $this->sendErrorResponse($message);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['message' => $message]),
            $response->getContent()
        );
    }
}
