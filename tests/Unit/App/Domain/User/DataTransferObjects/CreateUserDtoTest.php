<?php

namespace Tests\Unit\App\Domain\User\DataTransferObjects;

use Tests\DtoTestCase;
use Illuminate\Http\Request;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\DataTransferObjects\CreateUserDto;
use App\Domain\User\Enums\UserTypeEnum;

class CreateUserDtoTest extends DtoTestCase
{
    /**
     * @group dtos
     * @group user
     */
    public function test_can_create_from_array_with_snakecase_keys(): void
    {
        $this->runCreationFromSnakecaseArrayAssertions(
            CreateUserDto::class,
            [
                'user_type_id' => UserTypeEnum::COMMON->value,
                'name' => fake()->name(),
                'document_type_id' => DocumentTypeEnum::CPF->value,
                'document_number' => fake()->cpf(false),
                'email' => fake()->unique()->safeEmail(),
                'password' => fake()->password(12),
                'starter_balance' => fake()->randomFloat(2, 10, 900)
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
            CreateUserDto::class,
            [
                'userTypeId' => UserTypeEnum::COMMON->value,
                'name' => fake()->name(),
                'documentTypeId' => DocumentTypeEnum::CPF->value,
                'documentNumber' => fake()->cpf(false),
                'email' => fake()->unique()->safeEmail(),
                'password' => fake()->password(12),
                'starterBalance' => fake()->randomFloat(2, 10, 900)
            ]
        );
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_empty_array(): void
    {
        $this->runCreationFromEmptyArrayAssertions(CreateUserDto::class);
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_array_with_invalid_enum_values(): void
    {
        $this->runCreationFromArrayWithInvalidEnumValuesAssertions(
            CreateUserDto::class,
            [
                'user_type_id' => 99,
                'name' => fake()->name(),
                'document_type_id' => 88,
                'document_number' => fake()->cpf(false),
                'email' => fake()->unique()->safeEmail(),
                'password' => fake()->password(12),
                'starter_balance' => fake()->randomFloat(2, 10, 900)
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
            CreateUserDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'user_type_id' => UserTypeEnum::COMMON->value,
                    'name' => fake()->name(),
                    'document_type_id' => DocumentTypeEnum::CPF->value,
                    'document_number' => fake()->cpf(false),
                    'email' => fake()->unique()->safeEmail(),
                    'password' => fake()->password(12),
                    'starter_balance' => fake()->randomFloat(2, 10, 900)
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
        $this->runCreationFromEmptyRequestAssertions(CreateUserDto::class);
    }

    /**
     * @group dtos
     * @group user
     */
    public function test_cannot_create_from_request_with_invalid_values(): void
    {
        $this->runCreationFromRequestWithInvalidValuesAssertions(
            CreateUserDto::class,
            Request::create(
                '/dummy',
                'POST',
                [
                    'user_type_id' => 99,
                    'name' => fake()->name(),
                    'document_type_id' => 88,
                    'document_number' => fake()->cpf(false),
                    'email' => fake()->unique()->safeEmail(),
                    'password' => fake()->password(12),
                    'starter_balance' => fake()->randomFloat(2, 10, 900)
                ]
            )
        );
    }
}
