<?php

namespace Tests\Unit\App\Domain\Transfer\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Enums\TransferStatusEnum;

class CreateTransferDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group transfer
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateTransferDto::class,
            [
                'payer_id' => 5,
                'payee_id' => 6,
                'amount' => 33.50,
                'transfer_status_id' => TransferStatusEnum::PENDING->value
            ]
        );
    }

    /**
     * @group dtos
     * @group transfer
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromCamelcaseArrayAssertions(
            CreateTransferDto::class,
            [
                'payerId' => 5,
                'payeeId' => 6,
                'amount' => 33.50,
                'transferStatusId' => TransferStatusEnum::PENDING->value
            ]
        );
    }

    /**
     * @group dtos
     * @group transfer
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateTransferDto::class);
    }

    /**
     * @group dtos
     * @group transfer
     */
    public function test_can_create_from_request(): void
    {
        $this->runCreationFromRequestAssertions(
            CreateTransferDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'payer_id' => 5,
                    'payee_id' => 6,
                    'amount' => 33.50,
                    'transfer_status_id' => TransferStatusEnum::PENDING->value
                ]
            )
        );
    }

    /**
     * @group dtos
     * @group transfer
     */
    public function test_cannot_create_from_empty_request(): void
    {
        $this->runCreationFromEmptyRequestAssertions(CreateTransferDto::class);
    }

    /**
     * @group dtos
     * @group transfer
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateTransferDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'payer_id' => 'abc',
                    'payee_id' => 'zxc',
                    'amount' => 33.50,
                    'transfer_status_id' => TransferStatusEnum::PENDING->value
                ]
            )
        );
    }
}
