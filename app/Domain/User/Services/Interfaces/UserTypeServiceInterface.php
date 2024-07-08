<?php

namespace App\Domain\User\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Domain\User\Models\UserType;

interface UserTypeServiceInterface
{
    public function firstById(int $id): ?UserType;

    public function getAll(array $columns = ['*']): Collection;
}
