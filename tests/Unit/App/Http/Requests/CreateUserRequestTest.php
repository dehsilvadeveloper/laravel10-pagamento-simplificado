<?php

namespace Tests\Unit\App\Http\Requests;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CreateUserRequest;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class CreateUserRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group requests
     * @group user
     */
    public function test_pass_with_valid_request(): void
    {
        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_missing_required_fields(): void
    {
        $data = [];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(6, $validator->errors());
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_non_existent_user_type(): void
    {
        $data = [
            'user_type_id' => 9999,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('user_type_id'));
        $this->assertEquals(
            [
                'The selected user type id is invalid.'
            ],
            $validator->errors()->get('user_type_id')
        );
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_invalid_max_size_name(): void
    {
        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->sentence(80),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('name'));
        $this->assertEquals(
            [
                'The name field must not be greater than 70 characters.'
            ],
            $validator->errors()->get('name')
        );
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_non_existent_document_type(): void
    {
        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => 9999,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('document_type_id'));
        $this->assertEquals(
            [
                'The selected document type id is invalid.'
            ],
            $validator->errors()->get('document_type_id')
        );
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_non_unique_document_number(): void
    {
        $documentNumber = fake()->cpf(false);

        User::factory()->create([
            'document_number' => $documentNumber
        ]);

        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => $documentNumber,
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('document_number'));
        $this->assertEquals(
            [
                'The document number has already been taken.'
            ],
            $validator->errors()->get('document_number')
        );
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_invalid_email(): void
    {
        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => 'invalid_format_email',
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertEquals(
            [
                'The email field must be a valid email address.'
            ],
            $validator->errors()->get('email')
        );
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_non_unique_email(): void
    {
        $email = fake()->unique()->safeEmail();

        User::factory()->create([
            'email' => $email
        ]);

        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => $email,
            'password' => fake()->password(12)
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('email'));
        $this->assertEquals(
            [
                'The email has already been taken.'
            ],
            $validator->errors()->get('email')
        );
    }

    /**
     * @group requests
     * @group user
     */
    public function test_fail_with_invalid_min_size_password(): void
    {
        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => 'TDw'
        ];

        $request = (new CreateUserRequest())->replace($data);
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertCount(1, $validator->errors());
        $this->assertTrue($validator->errors()->has('password'));
        $this->assertEquals(
            [
                'The password field must be at least 8 characters.'
            ],
            $validator->errors()->get('password')
        );
    }
}
