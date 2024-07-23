<?php

namespace App\Domain\Notification\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateNotificationDto extends BaseDto
{
    public function __construct(
        public int $recipientId,
        public string $type,
        public string $channel,
        public ?string $response
    ) {
    }
}
