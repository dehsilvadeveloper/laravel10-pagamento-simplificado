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
use App\Http\Controllers\Controller;
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
     * @responseField id integer The identifier of the user.
     * @responseField name string The name of the user.
     * @responseField user_type.id integer The identifier of the type of user.
     * @responseField user_type.name string The type of user.
     * @responseField document_type.id integer The identifier of the type of document.
     * @responseField document_type.name string The type of document.
     * @responseField document_number string The number of document.
     * @responseField email string The e-mail of the user.
     * @responseField wallet.id integer The identifier of the wallet of the user.
     * @responseField wallet.balance float The current balance of the wallet of the user.
     * @responseField created_at string The date and time in which the user was created.
     * @responseField updated_at string The date and time in which the user was last updated.
     * 
     * @response status=201 scenario=success {
     *      "message": "Transfer made with success.",
     *      "data": {
     *          "id": 17,
     *          "name": "Peter Parker",
     *          "user_type": {
     *              "id": 1,
     *              "name": "comum"
     *          },
     *          "document_type": {
     *              "id": 2,
     *              "name": "cpf"
     *          },
     *          "document_number": "06633022000",
     *          "email": "peter.parker@marvel.com",
     *          "wallet": {
     *              "id": 1,
     *              "balance": 150.10
     *          }
     *          "created_at": "2024-07-12 15:42:18",
     *          "updated_at": "2024-07-12 15:42:18"
     *      }
     * }
     *
     * @response status=401 scenario="unauthenticated" {
     *      "message": "Unauthenticated."
     * }
     * 
     * @response status=422 scenario="validation error" {
     *      "message": "The user type id field is required. (and 5 more errors)",
     *      "errors": {
     *          "user_type_id": [
     *              "The user type id field is required."
     *          ],
     *          "name": [
     *              "The name field is required."
     *          ],
     *          "document_type_id": [
     *              "The document type id field is required."
     *          ],
     *          "document_number": [
     *              "The document number field is required."
     *          ],
     *          "email": [
     *              "The email field is required."
     *          ],
     *          "password": [
     *              "The password field is required."
     *          ],
     *          "starter_balance": [
     *              "The starter balance field is required."
     *          ]
     *      }
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
