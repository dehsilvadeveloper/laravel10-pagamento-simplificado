<?php

namespace App\Domain\DocumentType\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateDocumentTypeDto extends BaseDto
{
    public function __construct(
        public string $name
    ) {
    }
}
