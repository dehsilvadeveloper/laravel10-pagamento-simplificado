<?php

namespace Tests\Feature\Authentication;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Domain\ApiUser\Models\ApiUser;

class GetAuthenticatedApiUserTest extends TestCase
{
    /**
     * @group auth
     */
    public function test_can_get_authenticated_user_data(): void
    {
        $user = ApiUser::factory()->create([
            'password' => 'defaultpassword'
        ]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('auth.me'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at'
            ]
        ]);
        $response->assertJsonMissing(['password']);

        $this->assertNotEmpty($response['data']['id']);
        $this->assertNotEmpty($response['data']['name']);
        $this->assertNotEmpty($response['data']['email']);
        $this->assertEquals($user->id, $response['data']['id']);
        $this->assertEquals($user->name, $response['data']['name']);
        $this->assertEquals($user->email, $response['data']['email']);
    }

    /**
     * @group auth
     */
    public function test_cannot_get_data_of_unauthenticated_user(): void
    {
        $response = $this->getJson(route('auth.me'));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertExactJson(['message' => 'Unauthenticated.']);
    }
}
