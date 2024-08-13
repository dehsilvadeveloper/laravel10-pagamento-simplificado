<?php

namespace Tests\Unit\Database\Factories;

use Tests\TestCase;
use App\Domain\Transfer\Models\TransferStatus;

class TransferStatusFactoryTest extends TestCase
{
    /**
     * @group factories
     * @group transfer
     */
    public function test_can_create_a_model(): void
    {
        $model = TransferStatus::factory()->make();

        $this->assertInstanceOf(TransferStatus::class, $model);
    }
}
