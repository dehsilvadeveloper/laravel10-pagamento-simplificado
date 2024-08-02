<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Events\UserCreated;
use App\Domain\User\Models\User;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class CreateUserTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group user
     */
    public function test_can_create(): void
    {
        Event::fake();

        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12),
            'starter_balance' => fake()->randomFloat(2, 10, 900)
        ];

        $response = $this->postJson(route('user.create'), $data);

        Event::assertDispatched(UserCreated::class);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'name',
                'user_type' => [
                    'id',
                    'name'
                ],
                'document_type' => [
                    'id',
                    'name'
                ],
                'document_number',
                'email',
                'wallet' => [
                    'id',
                    'balance'
                ]
            ]
        ]);
        $response->assertJson([
            'message' => 'User created with success.',
            'data' => [
                'name' => $data['name'],
                'user_type' => [
                    'id' => $data['user_type_id'],
                    'name' => UserTypeEnum::COMMON->name(),
                ],
                'document_type' => [
                    'id' => $data['document_type_id'],
                    'name' => DocumentTypeEnum::CPF->name(),
                ],
                'document_number' => $data['document_number'],
                'email' => $data['email'],
                'wallet' => [
                    'balance' => $data['starter_balance']
                ]
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_create_without_data(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $response = $this->postJson(route('user.create'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'user_type_id',
                'name',
                'document_type_id',
                'document_number',
                'email',
                'password',
                'starter_balance'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'user_type_id' => ['The user type id field is required.'],
                'name' => ['The name field is required.'],
                'document_type_id' => ['The document type id field is required.'],
                'document_number' => ['The document number field is required.'],
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
                'starter_balance' => ['The starter balance field is required.']
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_create_with_invalid_data(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $data = [
            'user_type_id' => 9999,
            'document_type_id' => 8888,
            'document_number' => fake()->cpf(false),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => fake()->password(12),
            'starter_balance' => fake()->randomFloat(2, 10, 900)
        ];

        $response = $this->postJson(route('user.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'user_type_id',
                'document_type_id'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'user_type_id' => ['The selected user type id is invalid.'],
                'document_type_id' => ['The selected document type id is invalid.']
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_create_with_non_unique_fields(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $documentNumber = fake()->cpf(false);
        $email = fake()->unique()->safeEmail();

        User::factory()->create([
            'document_number' => $documentNumber,
            'email' => $email
        ]);

        $data = [
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value,
            'document_number' => $documentNumber,
            'name' => fake()->name(),
            'email' => $email,
            'password' => fake()->password(12),
            'starter_balance' => fake()->randomFloat(2, 10, 900)
        ];

        $response = $this->postJson(route('user.create'), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'document_number',
                'email'
            ]
        ]);
        $response->assertJson([
            'errors' => [
                'document_number' => ['The document number has already been taken.'],
                'email' => ['The email has already been taken.']
            ]
        ]);
    }
}
