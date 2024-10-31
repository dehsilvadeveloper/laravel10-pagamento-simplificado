<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group Mocked External Notifier
 *
 * Endpoints for simulations related to sending external notifications
 */
class MockExternalNotifierController extends Controller
{
    /**
     * Simulate sending notification
     *
     * This endpoint simulates the possible responses for the external notifier ExtNotifier.
     *
     * @responseField status string The status of the response. Can be "success"or "fail".
     * @responseField data.sent boolean The response of the sending process. Can be true or false.
     *
     * @response status=200 scenario=success {
     *      "status": "success",
     *      "data": [
     *          {
     *              "sent": true
     *          }
     *      ]
     * }
     * 
     * @response status=403 scenario=forbidden {
     *      "status": "fail",
     *      "data": [
     *          {
     *              "sent": false
     *          }
     *      ]
     * }
     *
     */
    public function simulateNotify(Request $request): JsonResponse
    {
        $responses = [
            ['status' => 'success', 'data' => ['sent' => true]],
            ['status' => 'fail', 'data' => ['sent' => false]]
        ];

        $response = $responses[array_rand($responses)];

        return response()->json(
            data: $response,
            status: ($response['status'] == 'fail') ? Response::HTTP_FORBIDDEN : Response::HTTP_OK
        );
    }
}
