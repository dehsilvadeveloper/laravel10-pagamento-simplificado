<?php

namespace App\Domain\User\DataTransferObjects;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use App\Domain\Common\DataTransferObjects\BaseDto;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;

class CreateUserDto extends BaseDto
{
    public function __construct(
        #[WithCast(EnumCast::class)]
        public UserTypeEnum $userTypeId,
        public string $name,
        #[WithCast(EnumCast::class)]
        public DocumentTypeEnum $documentTypeId,
        public string $documentNumber,
        public string $email,
        public string $password
    ) {
    }
}
