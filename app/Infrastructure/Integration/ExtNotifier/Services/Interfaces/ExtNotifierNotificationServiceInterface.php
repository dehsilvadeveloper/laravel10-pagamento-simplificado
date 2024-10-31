<?php

namespace App\Infrastructure\Integration\ExtNotifier\Services\Interfaces;

use App\Infrastructure\Integration\ExtNotifier\DataTransferObjects\SendNotificationDto;
use Illuminate\Http\Client\Response as ClientResponse;

interface ExtNotifierNotificationServiceInterface
{
    public function notify(SendNotificationDto $dto): ClientResponse;
}
