<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\User\Models\User;
use App\Domain\Wallet\Models\Wallet;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class ListUserTest extends TestCase
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
    public function test_can_get_list_of_records(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $recordsCount = 3;

        User::factory()
            ->count($recordsCount)
            ->create()
            ->each(function(User $user) {
                Wallet::factory()->create(['user_id' => $user->id]);
            });

        $response = $this->getJson(route('user.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
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
            ]
        ]);
        $response->assertJsonCount($recordsCount, 'data');
    }

    /**
     * @group user
     */
    public function test_can_get_empty_list_of_records(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $response = $this->getJson(route('user.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data'
        ]);
        $response->assertJsonCount(0, 'data');
    }
}
