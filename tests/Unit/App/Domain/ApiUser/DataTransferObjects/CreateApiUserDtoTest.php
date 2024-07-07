<?php

namespace Tests\Unit\App\Domain\ApiUser\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\ApiUser\DataTransferObjects\CreateApiUserDto;

class CreateApiUserDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group api_user
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateApiUserDto::class,
            [
                'name' => 'Default App',
                'email' => 'default@app.com',
                'password' => 'defaultpassword'
            ]
        );
    }

    /**
     * @group dtos
     * @group api_user
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            CreateApiUserDto::class,
            [
                'name' => 'Default App',
                'email' => 'default@app.com',
                'password' => 'defaultpassword'
            ]
        );
    }

    /**
     * @group dtos
     * @group api_user
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateApiUserDto::class);
    }

    /**
     * @group dtos
     * @group api_user
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            CreateApiUserDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'name' => 'Default App',
                    'email' => 'default@app.com',
                    'password' => 'defaultpassword'
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group api_user
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(CreateApiUserDto::class);
    }

    /**
     * @group dtos
     * @group api_user
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateApiUserDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'name' => 123.50,
                    'email' => 123.50,
                    'password' => 123.50
                ]
            )
        );
    }
}
