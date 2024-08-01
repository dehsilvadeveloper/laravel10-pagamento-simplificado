<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\DocumentType\Enums\DocumentTypeEnum;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class GetUserTest extends TestCase
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
    public function test_can_find_by_id(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $existingRecord = User::factory()->create([
            'user_type_id' => UserTypeEnum::COMMON->value,
            'document_type_id' => DocumentTypeEnum::CPF->value
        ]);

        Wallet::factory()->for($existingRecord)->create([
            'balance' => fake()->randomFloat(2, 10, 900)
        ]);

        $response = $this->getJson(route('user.show', ['id' => $existingRecord->id]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
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
            'data' => [
                'name' => $existingRecord->name,
                'user_type' => [
                    'id' => $existingRecord->user_type_id,
                    'name' => UserTypeEnum::COMMON->name(),
                ],
                'document_type' => [
                    'id' => $existingRecord->document_type_id,
                    'name' => DocumentTypeEnum::CPF->name(),
                ],
                'document_number' => $existingRecord->document_number,
                'email' => $existingRecord->email
            ]
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_find_a_nonexistent_record(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $response = $this->getJson(route('user.show', ['id' => 9999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
        $response->assertJson([
            'message' => 'The user could not be found.'
        ]);
    }
}
