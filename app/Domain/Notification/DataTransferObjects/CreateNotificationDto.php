<?php

namespace App\Domain\Notification\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class CreateNotificationDto extends BaseDto
{
    public function __construct(
        public int $recipientId,
        public mixed $type,
        public string $channel,
        public mixed $response
    ) {
    }
}
