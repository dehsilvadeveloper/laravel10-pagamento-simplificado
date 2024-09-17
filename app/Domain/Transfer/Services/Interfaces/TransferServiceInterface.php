<?php

namespace App\Domain\Transfer\Services\Interfaces;

use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\ValueObjects\TransferParamsObject;

interface TransferServiceInterface
{
    public function transfer(TransferParamsObject $params): Transfer;
}
