<?php

namespace App\Domain\ApiUser\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateApiUserDto extends BaseDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }
}
