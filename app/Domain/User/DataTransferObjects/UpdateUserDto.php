<?php

namespace App\Domain\User\DataTransferObjects;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Optional;
use App\Domain\Common\DataTransferObjects\BaseDto;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;

class UpdateUserDto extends BaseDto
{
    public function __construct(
        #[WithCast(EnumCast::class)]
        public UserTypeEnum|Optional $userTypeId,
        public string|Optional $name,
        #[WithCast(EnumCast::class)]
        public DocumentTypeEnum|Optional $documentTypeId,
        public string|Optional $documentNumber,
        public string|Optional $email,
        public string|Optional $password
    ) {
    }
}
