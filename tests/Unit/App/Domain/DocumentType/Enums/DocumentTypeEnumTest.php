<?php

namespace Tests\Unit\App\Domain\DocumentType\Enums;

use Tests\TestCase;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;

class DocumentTypeEnumTest extends TestCase
{
    /**
     * @group enums
     * @group document_type
     */
    public function test_can_get_enum_values(): void
    {
        $this->assertEquals(1, DocumentTypeEnum::CNPJ->value);
        $this->assertEquals(2, DocumentTypeEnum::CPF->value);
    }

    /**
     * @group enums
     * @group document_type
     */
    public function test_can_get_enum_names(): void
    {
        $this->assertEquals(
            config('document_types.default.0.name'),
            DocumentTypeEnum::CNPJ->name()
        );
        $this->assertEquals(
            config('document_types.default.1.name'),
            DocumentTypeEnum::CPF->name()
        );
    }
}
