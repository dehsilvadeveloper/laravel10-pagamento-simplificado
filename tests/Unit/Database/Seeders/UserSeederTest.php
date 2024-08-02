<?php

namespace Tests\Unit\Database\Seeders;

use Database\Seeders\DocumentTypeSeeder;
use Tests\TestCase;
use Database\Seeders\UserSeeder;
use Database\Seeders\UserTypeSeeder;

class UserSeederTest extends TestCase
{
    /**
     * @group seeders
     * @group user
     */
    public function test_can_seed_users_into_database(): void
    {
        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
        $this->seed(UserSeeder::class);

        foreach (config('users.default') as $data) {
            unset($data['user']['password']);
            $this->assertDatabaseHas('users', $data['user']);
        }

        $this->assertDatabaseCount('wallets', count(config('users.default')));
    }
}
