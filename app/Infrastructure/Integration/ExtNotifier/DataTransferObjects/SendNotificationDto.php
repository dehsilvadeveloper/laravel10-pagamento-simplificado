<?php

namespace App\Infrastructure\Integration\ExtNotifier\DataTransferObjects;

use App\Domain\Common\DataTransferObjects\BaseDto;

class SendNotificationDto extends BaseDto
{
    public function __construct(
        public string $recipient,
        public string $message
    ) {
    }
}
