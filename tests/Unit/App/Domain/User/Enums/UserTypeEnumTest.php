<?php

namespace Tests\Unit\App\Domain\User\Enums;

use Tests\TestCase;
use App\Domain\User\Enums\UserTypeEnum;

class UserTypeEnumTest extends TestCase
{
    /**
     * @group enums
     * @group user
     */
    public function test_can_get_enum_values(): void
    {
        $this->assertEquals(1, UserTypeEnum::COMMON->value);
        $this->assertEquals(2, UserTypeEnum::SHOPKEEPER->value);
    }

    /**
     * @group enums
     * @group user
     */
    public function test_can_get_enum_names(): void
    {
        $this->assertEquals(
            config('user_types.default.0.name'),
            UserTypeEnum::COMMON->name()
        );
        $this->assertEquals(
            config('user_types.default.1.name'),
            UserTypeEnum::SHOPKEEPER->name()
        );
    }
}
