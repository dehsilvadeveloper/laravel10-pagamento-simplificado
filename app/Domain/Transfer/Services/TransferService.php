<?php

namespace App\Domain\Transfer\Services;

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
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;

class TransferService implements TransferServiceInterface
{
    public function __construct(
        private TransferRepositoryInterface $transferRepository,
        private WalletRepositoryInterface $walletRepository
    ) {
    }

    public function transfer(TransferParamsObject $params): Transfer
    {
        try {
            $transfer = $this->registerTransfer($params);

            if (!$this->authorizeTransfer($transfer)) {
                // TODO: atualizar registro na tabela "transfers" para status "não autorizado".
                throw new UnauthorizedTransferException();
            }

            $processedTransfer = $this->executeTransfer();

            // TODO: retornar no final o registro da tabela "transfers" atualizado.
            return app(Transfer::class);
        } catch (Throwable $exception) {
            Log::error(
                '[TransferService] Failed to execute the transfer between the users as requested.',
                [
                    'error_message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'data' => [
                        'received_dto' => $params->toArray() ?? null
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

    private function authorizeTransfer(): void
    {

    }

    private function executeTransfer(): void
    {
        try {
            DB::beginTransaction();

            // TODO: retirar montante da carteira do "payer".
            // TODO: adicionar montante na carteira do "payee".
            // TODO: atualizar registro na tabela "transfers" para status "concluído".

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            // TODO: atualizar registro na tabela "transfers" para status "erro".

            throw $exception;
        }
    }
}
