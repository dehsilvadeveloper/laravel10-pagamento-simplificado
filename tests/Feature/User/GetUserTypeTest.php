<?php

namespace Tests\Feature\User;

use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\User\Models\UserType;

class GetUserTypeTest extends TestCase
{
    /**
     * @group user
     */
    public function test_can_get_list_of_records(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $recordsCount = 3;
        UserType::factory()->count($recordsCount)->create();

        $response = $this->getJson(route('user-type.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name'
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

        $response = $this->getJson(route('user-type.index'));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data'
        ]);
        $response->assertJsonCount(0, 'data');
    }

    /**
     * @group user
     */
    public function test_can_find_by_id(): void
    {
        $apiUser = ApiUser::factory()->create();

        Sanctum::actingAs($apiUser, ['*']);

        $existingRecord = UserType::factory()->create();

        $response = $this->getJson(route('user-type.show', ['id' => $existingRecord->id]));

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name'
            ]
        ]);
    }
}
