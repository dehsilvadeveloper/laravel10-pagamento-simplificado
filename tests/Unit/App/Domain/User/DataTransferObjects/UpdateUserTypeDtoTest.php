<?php

namespace Tests\Unit\App\Domain\User\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\User\DataTransferObjects\UpdateUserTypeDto;

class UpdateUserTypeDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            UpdateUserTypeDto::class,
            [
                'name' => 'comum'
            ]
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            UpdateUserTypeDto::class,
            [
                'name' => 'comum'
            ]
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            UpdateUserTypeDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'name' => 'comum'
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            UpdateUserTypeDto::class,
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
