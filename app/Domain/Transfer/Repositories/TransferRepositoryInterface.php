<?php

namespace App\Domain\Transfer\Repositories;

use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Enums\TransferStatusEnum;
use App\Domain\Transfer\Models\Transfer;

interface TransferRepositoryInterface
{
    public function create(CreateTransferDto $dto): Transfer;

    public function updateStatus(int $id, TransferStatusEnum $newStatus): Transfer;
}
