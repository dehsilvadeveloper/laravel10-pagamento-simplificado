<?php

namespace App\Domain\Transfer\Services\Interfaces;

use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Models\Transfer;

interface TransferServiceInterface
{
    public function transfer(CreateTransferDto $transferDto): Transfer;
}
