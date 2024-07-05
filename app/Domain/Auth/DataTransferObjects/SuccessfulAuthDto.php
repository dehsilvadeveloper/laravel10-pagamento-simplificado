<?php

namespace App\Domain\Auth\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class SuccessfulAuthDto extends BaseDto
{
    public function __construct(
        public string $accessToken,
        public string $tokenType,
        public string $expiresAt
    ) {
    }
}
