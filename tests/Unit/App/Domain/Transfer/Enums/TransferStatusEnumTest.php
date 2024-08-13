<?php

namespace Tests\Unit\App\Domain\Transfer\Enums;

use Tests\TestCase;
use App\Domain\Transfer\Enums\TransferStatusEnum;

class TransferStatusEnumTest extends TestCase
{
    /**
     * @group enums
     * @group transfer
     */
    public function test_can_get_enum_values(): void
    {
        $this->assertEquals(1, TransferStatusEnum::PENDING->value);
        $this->assertEquals(2, TransferStatusEnum::COMPLETED->value);
        $this->assertEquals(3, TransferStatusEnum::UNAUTHORIZED->value);
        $this->assertEquals(4, TransferStatusEnum::ERROR->value);
    }

    /**
     * @group enums
     * @group transfer
     */
    public function test_can_get_enum_names(): void
    {
        $this->assertEquals(
            config('transfer_statuses.default.0.name'),
            TransferStatusEnum::PENDING->name()
        );
        $this->assertEquals(
            config('transfer_statuses.default.1.name'),
            TransferStatusEnum::COMPLETED->name()
        );
        $this->assertEquals(
            config('transfer_statuses.default.2.name'),
            TransferStatusEnum::UNAUTHORIZED->name()
        );
        $this->assertEquals(
            config('transfer_statuses.default.3.name'),
            TransferStatusEnum::ERROR->name()
        );
    }
}
