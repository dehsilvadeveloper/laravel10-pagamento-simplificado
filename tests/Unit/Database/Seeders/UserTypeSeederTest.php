<?php

namespace Tests\Unit\Database\Seeders;

use Tests\TestCase;
use Database\Seeders\UserTypeSeeder;

class UserTypeSeederTest extends TestCase
{
    /**
     * @group seeders
     * @group user
     */
    public function test_can_seed_user_types_into_database(): void
    {
        $this->seed(UserTypeSeeder::class);

        foreach (config('user_types.default') as $userType) {
            $this->assertDatabaseHas('user_types', $userType);
        }
    }
}
