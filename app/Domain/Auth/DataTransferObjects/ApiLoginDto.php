<?php

namespace App\Domain\Auth\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class ApiLoginDto extends BaseDto
{
    public function __construct(
        public string $email,
        public string $password
    ) {
    }
}
