<?php

namespace App\Domain\DocumentType\Enums;

enum DocumentTypeEnum: int
{
    case CNPJ = 1;
    case CPF = 2;

    public function name(): string
    {
        return match($this) {
            self::CNPJ => config('document_types.default.0.name'),
            self::CPF => config('document_types.default.1.name'),
        };
    }
}
