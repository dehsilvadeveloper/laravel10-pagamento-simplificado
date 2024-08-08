<?php

namespace Tests\Unit\App\Domain\TransferAuthorization\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;

class AuthorizeTransferDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            AuthorizeTransferDto::class,
            [
                'transfer_id' => 1,
                'payer_id' => 5,
                'payee_id' => 6,
                'amount' => 25.50
            ]
        );
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            AuthorizeTransferDto::class,
            [
                'transferId' => 1,
                'payerId' => 5,
                'payeeId' => 6,
                'amount' => 25.50
            ]
        );
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(AuthorizeTransferDto::class);
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            AuthorizeTransferDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'transfer_id' => 1,
                    'payer_id' => 5,
                    'payee_id' => 6,
                    'amount' => 25.50
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(AuthorizeTransferDto::class);
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            AuthorizeTransferDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'transfer_id' => 1,
                    'payer_id' => 'abc',
                    'payee_id' => 'xyz',
                    'amount' => 25.50
                ]
            )
        );
    }
}
