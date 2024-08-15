<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Traits\Http\ApiResponse;

/**
 * @group Mock External Authorizer
 *
 * Endpoints for simulations related to external authorization of transfers
 */
class MockExternalAuthorizationController extends Controller
{
    use ApiResponse;

    /**
     * Simulate authorization
     *
     * This endpoint simulates the possible responses for the external authorizer ExtAutho.
     *
     * @responseField status string The status of the response. Can be "success"or "fail".
     * @responseField data.authorization boolean The response of the authorization. Can be true or false.
     *
     * @response status=200 scenario=success {
     *      "status": "success",
     *      "data": [
     *          {
     *              "authorization": true
     *          }
     *      ]
     * }
     * 
     * @response status=403 scenario=forbidden {
     *      "status": "fail",
     *      "data": [
     *          {
     *              "authorization": false
     *          }
     *      ]
     * }
     *
     */
    public function simulateAuthorize(): JsonResponse
    {
        $responses = [
            ['status' => 'success', 'data' => ['authorization' => true]],
            ['status' => 'fail', 'data' => ['authorization' => false]]
        ];

        $response = $responses[array_rand($responses)];

        return $this->sendSuccessResponse(
            data: $response,
            code: ($response['status'] == 'fail') ? Response::HTTP_FORBIDDEN : Response::HTTP_OK
        );
    }
}
