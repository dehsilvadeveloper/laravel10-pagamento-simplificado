<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\DocumentType\Models\DocumentType;

class DocumentTypeFactoryTest extends TestCase
{
    /**
     * @group factories
     * @group document_type
     */
    public function test_can_create_a_model(): void
    {
        $model = DocumentType::factory()->make();

        $this->assertInstanceOf(DocumentType::class, $model);
    }
}
