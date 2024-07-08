<?php

namespace App\Domain\User\DataTransferObjects;

use Spatie\LaravelData\Optional;
use App\Domain\Common\DataTransferObjects\BaseDto;

class UpdateUserTypeDto extends BaseDto
{
    public function __construct(
        public string|Optional $name
    ) {
    }
}
