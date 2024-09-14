<?php

namespace App\Domain\Transfer\Services;

use Carbon\Carbon;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Enums\TransferStatusEnum;
use App\Domain\Transfer\Exceptions\TransferFailedException;
use App\Domain\Transfer\Exceptions\UnauthorizedTransferException;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Repositories\TransferRepositoryInterface;
use App\Domain\Transfer\Services\Interfaces\TransferServiceInterface;
use App\Domain\Transfer\ValueObjects\TransferParamsObject;
use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;
use App\Domain\TransferAuthorization\Services\Interfaces\TransferAuthorizerServiceInterface;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;

class TransferService implements TransferServiceInterface
{
    public function __construct(
        private TransferAuthorizerServiceInterface $transferAuthorizationService,
        private TransferRepositoryInterface $transferRepository,
        private WalletRepositoryInterface $walletRepository
    ) {
    }

    public function transfer(TransferParamsObject $params): Transfer
    {
        try {
            $transfer = $this->registerTransfer($params);

            if (!$this->authorizeTransfer($transfer)) {
                $this->transferRepository->updateStatus($transfer->id, TransferStatusEnum::UNAUTHORIZED);

                throw new UnauthorizedTransferException();
            }

            $this->updateTransferAuthorizationDate($transfer);

            return $this->executeTransfer($transfer);
        } catch (UnauthorizedTransferException $exception) {
            Log::error(
                '[TransferService] The transfer was not authorized.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_object' => $params->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw $exception;
        } catch (Throwable $exception) {
            Log::error(
                '[TransferService] Failed to execute the transfer between the users as requested.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_object' => $params->toArray() ?? null
                    ],
                    'stack_trace' => $exception->getTrace()
                ]
            );

            throw new TransferFailedException();
        }
    }

    private function registerTransfer(TransferParamsObject $params): Transfer
    {
        return $this->transferRepository->create(
            CreateTransferDto::from([
                'payer_id' => $params->getPayerId(),
                'payee_id' => $params->getPayeeId(),
                'amount' => $params->getAmount(),
                'transfer_status_id' => TransferStatusEnum::PENDING
            ]
        ));
    }

    private function authorizeTransfer(Transfer $transfer): bool
    {
        return $this->transferAuthorizationService->authorize(
            AuthorizeTransferDto::from([
                'transfer_id' => $transfer->id,
                'payer_id' => $transfer->payer_id,
                'payee_id' => $transfer->payee_id,
                'amount' => $transfer->amount
            ])
        );
    }

    private function updateTransferAuthorizationDate(Transfer $transfer): void
    {
        $this->transferRepository->updateAuthorizationDate($transfer->id, Carbon::now());
    }

    private function executeTransfer(Transfer $transfer): Transfer
    {
        try {
            DB::beginTransaction();

            $this->walletRepository->decrementById($transfer->payer->wallet->id, 'balance', $transfer->amount);
            $this->walletRepository->incrementById($transfer->payee->wallet->id, 'balance', $transfer->amount);

            $processedTransfer = $this->transferRepository->updateStatus($transfer->id, TransferStatusEnum::COMPLETED);

            DB::commit();

            // TODO: Incluir evento de notificação para PAYEE. Exemplo: event(new TransferReceived($user));

            return $processedTransfer;
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->transferRepository->updateStatus($transfer->id, TransferStatusEnum::ERROR);

            throw $exception;
        }
    }
}
