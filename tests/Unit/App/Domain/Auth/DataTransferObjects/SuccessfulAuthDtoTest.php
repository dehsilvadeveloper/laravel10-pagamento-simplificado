<?php

namespace Tests\Unit\App\Domain\Auth\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\Auth\DataTransferObjects\SuccessfulAuthDto;

class SuccessfulAuthDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group auth
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            SuccessfulAuthDto::class,
            [
                'access_token' => 'fake-token',
                'token_type' => 'Bearer',
                'expires_at' => now()->format('Y-m-d H:i:s')
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
            SuccessfulAuthDto::class,
            [
                'accessToken' => 'fake-token',
                'tokenType' => 'Bearer',
                'expiresAt' => now()->format('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(SuccessfulAuthDto::class);
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            SuccessfulAuthDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'access_token' => 'fake-token',
                    'token_type' => 'Bearer',
                    'expires_at' => now()->format('Y-m-d H:i:s')
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
        $this->runCreationFromEmptyRequestAssertions(SuccessfulAuthDto::class);
    }

    /**
     * @group dtos
     * @group auth
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            SuccessfulAuthDto::class,
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
