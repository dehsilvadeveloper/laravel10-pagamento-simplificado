<?php

namespace App\Domain\DocumentType\DataTransferObjects;

use Spatie\LaravelData\Optional;
use App\Domain\Common\DataTransferObjects\BaseDto;

class UpdateDocumentTypeDto extends BaseDto
{
    public function __construct(
        public string|Optional $name
    ) {
    }
}
