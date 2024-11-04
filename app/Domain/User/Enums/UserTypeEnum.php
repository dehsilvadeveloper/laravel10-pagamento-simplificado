<?php

namespace App\Domain\User\Enums;

enum UserTypeEnum: int
{
    case COMMON = 1;
    case SHOPKEEPER = 2;

    public function name(): string
    {
        return match ($this) {
            self::COMMON => config('user_types.default.0.name'),
            self::SHOPKEEPER => config('user_types.default.1.name')
        };
    }
}
