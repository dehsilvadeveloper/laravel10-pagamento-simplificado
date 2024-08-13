<?php

namespace App\Domain\TransferAuthorization\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateTransferAuthorizationResponseDto extends BaseDto
{
    public function __construct(
        public int $transferId,
        public string $response
    ) {
    }
}
