<?php

namespace Tests\Unit\Database\Seeders;

use Tests\TestCase;
use Database\Seeders\TransferStatusSeeder;

class TransferStatusSeederTest extends TestCase
{
    /**
     * @group seeders
     * @group transfer
     */
    public function test_can_seed_transfer_statuses_into_database(): void
    {
        $this->seed(TransferStatusSeeder::class);

        foreach (config('transfer_statuses.default') as $transferStatus) {
            $this->assertDatabaseHas('transfer_statuses', $transferStatus);
        }
    }
}
