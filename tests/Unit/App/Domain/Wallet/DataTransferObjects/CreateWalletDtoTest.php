<?php

namespace Tests\Unit\App\Domain\Wallet\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\Wallet\DataTransferObjects\CreateWalletDto;

class CreateWalletDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group wallet
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateWalletDto::class,
            [
                'user_id' => 1,
                'balance' => 20.55
            ]
        );
    }

    /**
     * @group dtos
     * @group wallet
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            CreateWalletDto::class,
            [
                'userId' => 1,
                'balance' => 20.55
            ]
        );
    }

    /**
     * @group dtos
     * @group wallet
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateWalletDto::class);
    }

    /**
     * @group dtos
     * @group wallet
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            CreateWalletDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'user_id' => 1,
                    'balance' => 20.55
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group wallet
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(CreateWalletDto::class);
    }

    /**
     * @group dtos
     * @group wallet
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateWalletDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'user_id' => 'abc',
                    'balance' => 20.55
                ]
            )
        );
    }
}
