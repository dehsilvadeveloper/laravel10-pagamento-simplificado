<?php

namespace App\Domain\Transfer\DataTransferObjects;

use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use App\Domain\Common\DataTransferObjects\BaseDto;
use App\Domain\Transfer\Enums\TransferStatusEnum;

class CreateTransferDto extends BaseDto
{
    public function __construct(
        public int $payerId,
        public int $payeeId,
        public float|int $amount,
        #[WithCast(EnumCast::class)]
        public TransferStatusEnum $transferStatusId
    ) {
    }
}
