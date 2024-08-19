<?php

namespace App\Domain\Transfer\Services;

use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Services\Interfaces\TransferServiceInterface;
use App\Domain\User\Repositories\UserRepositoryInterface;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;

class TransferService implements TransferServiceInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository
    ) {
    }

    public function transfer(CreateTransferDto $transferDto): Transfer
    {
        // TODO: Implement transfer logic
        return app(Transfer::class);
    }
}
