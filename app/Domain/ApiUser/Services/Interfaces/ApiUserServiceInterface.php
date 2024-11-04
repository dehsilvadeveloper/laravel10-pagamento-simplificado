<?php

namespace App\Domain\ApiUser\Services\Interfaces;

use App\Domain\ApiUser\Models\ApiUser;

interface ApiUserServiceInterface
{
    public function firstById(int $id): ?ApiUser;

    public function firstByEmail(string $email): ?ApiUser;
}
