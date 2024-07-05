<?php

namespace Tests\Unit\App\Domain\DocumentType\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\DocumentType\DataTransferObjects\UpdateDocumentTypeDto;

class UpdateDocumentTypeDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group document_type
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            UpdateDocumentTypeDto::class,
            [
                'name' => 'cnh'
            ]
        );
    }

    /**
     * @group dtos
     * @group document_type
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            UpdateDocumentTypeDto::class,
            [
                'name' => 'cnh'
            ]
        );
    }

    /**
     * @group dtos
     * @group document_type
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            UpdateDocumentTypeDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'name' => 'cnh'
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group document_type
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            UpdateDocumentTypeDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'name' => 123.50
                ]
            )
        );
    }
}
