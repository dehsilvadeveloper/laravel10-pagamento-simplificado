<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\Transfer\Models\Transfer;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;

class TransferFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
        $this->seed(TransferStatusSeeder::class);
    }

    /**
     * @group factories
     * @group transfer
     */
    public function test_can_create_a_model(): void
    {
        $model = Transfer::factory()->make();

        $this->assertInstanceOf(Transfer::class, $model);
    }
}
