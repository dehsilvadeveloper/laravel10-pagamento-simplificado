<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\User\Models\User;

class UserFactoryTest extends TestCase
{
    /**
     * @group factories
     * @group user
     */
    public function test_can_create_a_model(): void
    {
        $model = User::factory()->make();

        $this->assertInstanceOf(User::class, $model);
    }
}
