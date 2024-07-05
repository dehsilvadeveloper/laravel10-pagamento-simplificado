<?php

namespace Tests\Unit\Database\Seeders;

use Tests\TestCase;
use Database\Seeders\DocumentTypeSeeder;

class DocumentTypeSeederTest extends TestCase
{
    /**
     * @group seeders
     * @group document_type
     */
    public function test_can_seed_document_types_into_database(): void
    {
        $this->seed(DocumentTypeSeeder::class);

        foreach (config('document_types.default') as $documentType) {
            $this->assertDatabaseHas('document_types', $documentType);
        }
    }
}
