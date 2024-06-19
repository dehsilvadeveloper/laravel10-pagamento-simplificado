<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\ApiUser\Models\ApiUser;

class ApiUserFactoryTest extends TestCase
{
    /**
     * @group factories
     * @group api_user
     */
    public function test_can_create_a_model(): void
    {
        $model = ApiUser::factory()->make();

        $this->assertInstanceOf(ApiUser::class, $model);
    }
}