<?php

namespace App\Domain\Wallet\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateWalletDto extends BaseDto
{
    public function __construct(
        public int $userId,
        public float $balance
    ) {
    }
}
