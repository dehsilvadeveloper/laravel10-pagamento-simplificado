<?php

namespace Tests\Unit\App\Domain\DocumentType\Models;

use Tests\ModelTestCase;
use Tests\TestHelpers\DataTransferObjects\ModelConfigurationAssertionParamsDto;
use App\Domain\DocumentType\Models\DocumentType;

class DocumentTypeModelTest extends ModelTestCase
{
    /**
     * @group models
     * @group document_type
     */
    public function test_has_valid_configuration(): void
    {
        $dto = ModelConfigurationAssertionParamsDto::from([
            'model' => new DocumentType(),
            'fillable' => ['name'],
            'hidden' => [],
            'casts' => [
                'id' => 'int'
            ],
            'table' => 'document_types'
        ]);

        $this->runConfigurationAssertions($dto);
    }
}
