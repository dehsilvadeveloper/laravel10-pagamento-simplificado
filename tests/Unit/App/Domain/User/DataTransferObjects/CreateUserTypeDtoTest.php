<?php

namespace Tests\Unit\App\Domain\User\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\User\DataTransferObjects\CreateUserTypeDto;

class CreateUserTypeDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateUserTypeDto::class,
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
            CreateUserTypeDto::class,
            [
                'name' => 'comum'
            ]
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateUserTypeDto::class);
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            CreateUserTypeDto::class,
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
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(CreateUserTypeDto::class);
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateUserTypeDto::class,
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
