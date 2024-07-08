<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\User\Models\UserType;

class UserTypeFactoryTest extends TestCase
{
    /**
     * @group factories
     * @group user
     */
    public function test_can_create_a_model(): void
    {
        $model = UserType::factory()->make();

        $this->assertInstanceOf(UserType::class, $model);
    }
}
