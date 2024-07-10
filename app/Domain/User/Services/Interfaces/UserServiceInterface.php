<?php

namespace App\Domain\User\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Domain\User\DataTransferObjects\CreateUserDto;
use App\Domain\User\DataTransferObjects\UpdateUserDto;
use App\Domain\User\Models\User;

interface UserServiceInterface
{
    public function create(CreateUserDto $dto): ?User;

    public function update(int $id, UpdateUserDto $dto): User;

    public function delete(int $id): bool;

    public function firstById(int $id): ?User;

    public function firstByEmail(string $email): ?User;

    public function getAll(array $columns = ['*']): Collection;
}
