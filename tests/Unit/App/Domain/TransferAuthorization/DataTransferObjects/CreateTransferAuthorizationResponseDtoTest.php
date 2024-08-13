<?php

namespace Tests\Unit\App\Domain\TransferAuthorization\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\TransferAuthorization\DataTransferObjects\CreateTransferAuthorizationResponseDto;

class CreateTransferAuthorizationResponseDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateTransferAuthorizationResponseDto::class,
            [
                'transfer_id' => 1,
                'response' => json_encode([
                    'status' => 'success',
                    'authorization' => true
                ])
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
            CreateTransferAuthorizationResponseDto::class,
            [
                'transferId' => 1,
                'response' => json_encode([
                    'status' => 'success',
                    'authorization' => true
                ])
            ]
        );
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateTransferAuthorizationResponseDto::class);
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            CreateTransferAuthorizationResponseDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'transfer_id' => 1,
                    'response' => json_encode([
                        'status' => 'success',
                        'authorization' => true
                    ])
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
        $this->runCreationFromEmptyRequestAssertions(CreateTransferAuthorizationResponseDto::class);
    }

    /**
     * @group dtos
     * @group transfer_authorization
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateTransferAuthorizationResponseDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'transfer_id' => 'abc',
                    'response' => json_encode([
                        'status' => 'success',
                        'authorization' => true
                    ])
                ]
            )
        );
    }
}
