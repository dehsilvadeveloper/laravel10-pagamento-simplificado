<?php

namespace App\Domain\Wallet\Repositories;

use App\Domain\Wallet\DataTransferObjects\CreateWalletDto;
use App\Domain\Wallet\Models\Wallet;

interface WalletRepositoryInterface
{
    public function create(CreateWalletDto $dto): Wallet;

    public function incrementById(int $id, string $column, float|int $amount): Wallet;

    public function decrementById(int $id, string $column, float|int $amount): Wallet;
}
