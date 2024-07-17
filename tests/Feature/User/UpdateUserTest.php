<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class UpdateUserTest extends TestCase
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
    public function test_can_update(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $existingRecord = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value
        ]);

        $data = [
            'document_number' => fake()->cpf(false),
            'name' => 'Updated Name',
            'email' => fake()->unique()->safeEmail()
        ];

        $response = $this->patchJson(route('user.update', ['id' => $existingRecord->id]), $data);

        $response->assertStatus(Response::HTTP_OK);
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
                'email'
            ]
        ]);
        $response->assertJson([
            'message' => 'User updated with success.',
            'data' => [
                'name' => $data['name'],
                'user_type' => [
                    'id' => $existingRecord->user_type_id,
                    'name' => UserTypeEnum::COMMON->name(),
                ],
                'document_type' => [
                    'id' => $existingRecord->document_type_id,
                    'name' => DocumentTypeEnum::CPF->name(),
                ],
                'document_number' => $data['document_number'],
                'email' => $data['email']
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_update_a_nonexistent_record(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $data = [
            'document_number' => fake()->cpf(false),
            'name' => 'Updated Name',
            'email' => fake()->unique()->safeEmail()
        ];

        $response = $this->patchJson(route('user.update', ['id' => 9999]), $data);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
        $response->assertJson([
            'message' => 'The user could not be found.'
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_update_without_data(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $existingRecord = User::factory()->create();

        $response = $this->patchJson(route('user.update', ['id' => $existingRecord->id]), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure(['message']);
        $response->assertJson([
            'message' => 'You cannot update a resource without provide data.'
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_update_with_invalid_data(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $existingRecord = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value
        ]);

        $data = [
            'user_type_id' => 9999,
            'document_type_id' => 8888,
            'name' => 'Updated Name'
        ];

        $response = $this->patchJson(route('user.update', ['id' => $existingRecord->id]), $data);

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
    public function test_cannot_update_with_non_unique_fields(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $documentNumber = fake()->cpf(false);
        $email = fake()->unique()->safeEmail();

        User::factory()->create([
            'document_number' => $documentNumber,
            'email' => $email
        ]);

        $existingRecord = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value
        ]);

        $data = [
            'name' => 'Updated Name',
            'document_number' => $documentNumber,
            'email' => $email
        ];

        $response = $this->patchJson(route('user.update', ['id' => $existingRecord->id]), $data);

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
