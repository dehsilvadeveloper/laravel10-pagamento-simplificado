<?php

namespace Tests\Unit\App\Domain\User\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\DataTransferObjects\UpdateUserDto;
use App\Domain\User\Enums\UserTypeEnum;

class UpdateUserDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            UpdateUserDto::class,
            [
                'user_type_id' => UserTypeEnum::COMMON->value,
                'name' => fake()->name(),
                'document_number' => fake()->cpf(false),
                'document_type_id' => DocumentTypeEnum::CPF->value,
                'email' => fake()->unique()->safeEmail(),
                'password' => 'defaultpassword'
            ]
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_array_with_camelcase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            UpdateUserDto::class,
            [
                'userTypeId' => UserTypeEnum::COMMON->value,
                'name' => fake()->name(),
                'documentNumber' => fake()->cpf(false),
                'documentTypeId' => DocumentTypeEnum::CPF->value,
                'email' => fake()->unique()->safeEmail(),
                'password' => 'defaultpassword'
            ]
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_array_with_invalid_enum_values(): void
    {
        $this->runCreationFromArrayWithInvalidEnumValuesAssertions(
            UpdateUserDto::class,
            [
                'user_type_id' => 99,
                'name' => fake()->name(),
                'document_number' => fake()->cpf(false),
                'document_type_id' => 88,
                'email' => fake()->unique()->safeEmail(),
                'password' => 'defaultpassword'
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
            UpdateUserDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'user_type_id' => UserTypeEnum::COMMON->value,
                    'name' => fake()->name(),
                    'document_number' => fake()->cpf(false),
                    'document_type_id' => DocumentTypeEnum::CPF->value,
                    'email' => fake()->unique()->safeEmail(),
                    'password' => 'defaultpassword'
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
            UpdateUserDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'user_type_id' => 99,
                    'name' => fake()->name(),
                    'document_number' => fake()->cpf(false),
                    'document_type_id' => 88,
                    'email' => fake()->unique()->safeEmail(),
                    'password' => 'defaultpassword'
                ]
            )
        );
    }
}
