<?php

namespace Tests\Unit\App\Domain\Auth\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\Auth\DataTransferObjects\ApiLoginDto;

class ApiLoginDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group auth
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            ApiLoginDto::class,
            [
                'email' => 'default@app.com',
                'password' => 'defaultpassword'
            ]
        );
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            ApiLoginDto::class,
            [
                'email' => 'default@app.com',
                'password' => 'defaultpassword'
            ]
        );
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(ApiLoginDto::class);
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            ApiLoginDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'email' => 'default@app.com',
                    'password' => 'defaultpassword'
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(ApiLoginDto::class);
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            ApiLoginDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'access_token' => 123.50,
                    'token_type' => 123.50,
                    'expires_at' => 123.50
                ]
            )
        );
    }
}
