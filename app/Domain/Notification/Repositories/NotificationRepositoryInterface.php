<?php

namespace App\Domain\Notification\Repositories;

use App\Domain\Notification\DataTransferObjects\CreateNotificationDto;
use App\Domain\Notification\Models\Notification;

interface NotificationRepositoryInterface
{
    public function create(CreateNotificationDto $dto): Notification;
}
