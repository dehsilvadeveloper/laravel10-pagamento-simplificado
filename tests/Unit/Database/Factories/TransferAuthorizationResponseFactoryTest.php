<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;
use Database\Seeders\DocumentTypeSeeder;
use Database\Seeders\TransferStatusSeeder;
use Database\Seeders\UserTypeSeeder;

class TransferAuthorizationResponseFactoryTest extends TestCase
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
     * @group transfer_authorization
     */
    public function test_can_create_a_model(): void
    {
        $model = TransferAuthorizationResponse::factory()->make();

        $this->assertInstanceOf(TransferAuthorizationResponse::class, $model);
    }
}
