<?php

namespace App\Domain\Auth\Services\Interfaces;

use App\Domain\Auth\DataTransferObjects\ApiLoginDto;
use App\Domain\Auth\DataTransferObjects\SuccessfulAuthDto;

interface AuthServiceInterface
{
    public function login(ApiLoginDto $dto): SuccessfulAuthDto;
}