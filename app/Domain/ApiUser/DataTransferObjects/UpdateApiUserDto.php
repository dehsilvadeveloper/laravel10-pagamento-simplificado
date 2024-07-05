<?php

namespace App\Domain\ApiUser\DataTransferObjects;

use Spatie\LaravelData\Optional;
use App\Domain\Common\DataTransferObjects\BaseDto;

class UpdateApiUserDto extends BaseDto
{
    public function __construct(
        public string|Optional $name,
        public string|Optional $email,
        public string|Optional $password
    ) {
    }
}
