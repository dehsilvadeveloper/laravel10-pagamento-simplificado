<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\Wallet\Models\Wallet;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\UserTypeSeeder;

class WalletFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DocumentTypeSeeder::class);
        $this->seed(UserTypeSeeder::class);
    }

    /**
     * @group factories
     * @group wallet
     */
    public function test_can_create_a_model(): void
    {
        $model = Wallet::factory()->make();

        $this->assertInstanceOf(Wallet::class, $model);
    }
}
