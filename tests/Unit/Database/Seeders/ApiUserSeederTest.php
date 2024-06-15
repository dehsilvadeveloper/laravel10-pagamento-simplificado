<?php

namespace Tests\Unit\Database\Seeders;

use Tests\TestCase;
use Database\Seeders\ApiUserSeeder;

class ApiUserSeederTest extends TestCase
{
    /**
     * @group seeders
     * @group api_user
     */
    public function test_can_seed_api_users_into_database(): void
    {
        $this->seed(ApiUserSeeder::class);

        foreach (config('api_users.default') as $apiUser) {
            $this->assertDatabaseHas('api_users', $apiUser);
        }
    }
}
