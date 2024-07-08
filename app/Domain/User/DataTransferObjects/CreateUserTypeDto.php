<?php

namespace App\Domain\User\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateUserTypeDto extends BaseDto
{
    public function __construct(
        public string $name
    ) {
    }
}
