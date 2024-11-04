<?php

namespace App\Http\Controllers;

use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Domain\Transfer\Exceptions\InsufficientFundsException;
use App\Domain\Transfer\Exceptions\InvalidPayerException;
use App\Domain\Transfer\Exceptions\PayerNotFoundException;
use App\Domain\Transfer\Exceptions\TransferFailedException;
use App\Domain\Transfer\Exceptions\UnauthorizedTransferException;
use App\Domain\Transfer\Services\Interfaces\TransferServiceInterface;
use App\Domain\Transfer\ValueObjects\TransferParamsObject;
use App\Http\Requests\CreateTransferRequest;
use App\Http\Resources\TransferResource;
use App\Traits\Http\ApiResponse;

/**
 * @group Transfers
 *
 * Endpoints for managing transfers
 */
class TransferController extends Controller
{
    use ApiResponse;

    public function __construct(private TransferServiceInterface $transferService)
    {
    }

    /**
     * Create a transfer
     *
     * This endpoint allows you to create a new transfer.
     *
     * @responseField id integer The identifier of the transfer.
     * @responseField payer.id integer The identifier of the payer.
     * @responseField payer.name string The name of the payer.
     * @responseField payer.wallet.id integer The identifier of the wallet of the payer.
     * @responseField payer.wallet.balance float The current balance of the wallet of the payer.
     * @responseField payer.created_at string The date and time in which the payer was created.
     * @responseField payer.updated_at string The date and time in which the payer was last updated.
     * @responseField payee.id integer The identifier of the payee.
     * @responseField payee.name string The name of the payee.
     * @responseField payee.wallet.id integer The identifier of the wallet of the payee.
     * @responseField payee.wallet.balance float The current balance of the wallet of the payee.
     * @responseField payee.created_at string The date and time in which the payee was created.
     * @responseField payee.updated_at string The date and time in which the payee was last updated.
     * @responseField amount float The amount that was transferred.
     * @responseField status.id integer The identifier of the current status of the transfer.
     * @responseField status.name string The name of the current status of the transfer.
     * @responseField created_at string The date and time in which the transfer was created.
     * @responseField updated_at string The date and time in which the transfer was last updated.
     * @responseField created_at string The date and time in which the transfer was authorized.
     *
     * @response status=201 scenario=success {
     *      "message": "Transfer made with success.",
     *      "data": {
     *          "id": 10,
     *          "payer": {
     *              "id": 1,
     *              "name": "John Doe",
     *              "wallet": {
     *                  "id": 1,
     *                  "balance": 434.8
     *              },
     *              "created_at": "2024-07-02 11:19:30",
     *              "updated_at": "2024-07-02 11:19:30"
     *          },
     *          "payee": {
     *              "id": 2,
     *              "name": "Jane Doe",
     *              "wallet": {
     *                  "id": 2,
     *                  "balance": 475.8
     *              },
     *              "created_at": "2024-07-02 11:19:30",
     *              "updated_at": "2024-07-02 11:19:30"
     *          },
     *          "amount": "20.50",
     *          "status": {
     *              "id": 2,
     *              "name": "concluido"
     *          },
     *          "created_at": "2024-07-11 14:46:43",
     *          "updated_at": "2024-07-11 14:46:45",
     *          "authorized_at": "2024-07-11 14:46:45"
     *      }
     * }
     *
     * @response status=400 scenario="transfer general fail" {
     *      "message": "The transfer between the users has failed."
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     *
     * @response status=403 scenario="transfer unauthorized error" {
     *      "message": "The transfer was not authorized."
     * }
     *
     * @response status=422 scenario="validation error" {
     *      "message": "The payer field is required. (and 2 more errors)",
     *      "errors": {
     *          "payer": [
     *              "The payer field is required.",
     *              "The selected payer is invalid."
     *          ],
     *          "payee": [
     *              "The payee field is required.",
     *              "The selected payee is invalid."
     *          ],
     *          "value": [
     *              "The value field is required.",
     *              "The value field must be greater than 0."
     *          ]
     *      }
     * }
     *
     * @response status=422 scenario="invalid payer error" {
     *      "message": "The payer of a transfer cannot be of type shopkeeper."
     * }
     *
     * @response status=422 scenario="insufficient funds error" {
     *      "message": "The payer does not have sufficient funds in his wallet for this operation."
     * }
     *
     * @response status=500 scenario="unexpected error" {
     *      "message": "Internal Server Error."
     * }
     *
     * @authenticated
     *
     */
    public function create(CreateTransferRequest $request): JsonResponse
    {
        try {
            $validatedRequest = $request->safe()->all();
            $newTransfer = $this->transferService->transfer(
                new TransferParamsObject(
                    payerId: $validatedRequest['payer'],
                    payeeId: $validatedRequest['payee'],
                    amount: $validatedRequest['value']
                )
            );

            return $this->sendSuccessResponse(
                message: 'Transfer made with success.',
                data: new TransferResource($newTransfer),
                code: Response::HTTP_CREATED
            );
        } catch (Throwable $exception) {
            Log::error(
                '[TransferController] Failed to execute the transfer.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_data' => $request->all() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            $exceptionTypes = [
                PayerNotFoundException::class,
                InvalidPayerException::class,
                InsufficientFundsException::class,
                UnauthorizedTransferException::class,
                TransferFailedException::class
            ];

            $errorMessage = in_array(get_class($exception), $exceptionTypes)
                ? $exception->getMessage()
                : 'An error has occurred. Could not execute the transfer as requested.';

            return $this->sendErrorResponse(
                message: $errorMessage,
                code: $exception->getCode()
            );
        }
    }
}
