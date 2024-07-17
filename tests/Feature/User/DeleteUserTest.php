<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\User\Models\User;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class DeleteUserTest extends TestCase
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
    public function test_can_delete(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $existingRecord = User::factory()->create();

        $response = $this->deleteJson(route('user.delete', ['id' => $existingRecord->id]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(['message']);
        $response->assertJson([
            'message' => 'User deleted with success.'
        ]);
    }

    /**
     * @group user
     */
    public function test_cannot_delete_a_nonexistent_record(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $response = $this->deleteJson(route('user.delete', ['id' => 9999]));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['message']);
        $response->assertJson([
            'message' => 'The user could not be found.'
        ]);
    }
}
