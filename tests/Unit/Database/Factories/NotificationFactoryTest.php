<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\Notification\Models\Notification;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class NotificationFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group factories
     * @group notification
     */
    public function test_can_create_a_model(): void
    {
        $model = Notification::factory()->make();

        $this->assertInstanceOf(Notification::class, $model);
    }
}
