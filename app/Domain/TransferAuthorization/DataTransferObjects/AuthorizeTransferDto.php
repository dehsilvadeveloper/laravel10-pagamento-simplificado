<?php

namespace App\Domain\TransferAuthorization\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class AuthorizeTransferDto extends BaseDto
{
    public function __construct(
        public int $payerId,
        public int $payeeId,
        public float|int $amount
    ) {
    }
}
