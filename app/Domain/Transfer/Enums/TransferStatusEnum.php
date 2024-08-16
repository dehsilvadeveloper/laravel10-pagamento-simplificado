<?php

namespace App\Domain\Transfer\Enums;

enum TransferStatusEnum: int
{
    case PENDING = 1;
    case COMPLETED = 2;
    case UNAUTHORIZED = 3;
    case ERROR = 4;

    public function name(): string
    {
        return match($this) {
            self::PENDING => config('transfer_statuses.default.0.name'),
            self::COMPLETED => config('transfer_statuses.default.1.name'),
            self::UNAUTHORIZED => config('transfer_statuses.default.2.name'),
            self::ERROR => config('transfer_statuses.default.3.name')
        };
    }
}
