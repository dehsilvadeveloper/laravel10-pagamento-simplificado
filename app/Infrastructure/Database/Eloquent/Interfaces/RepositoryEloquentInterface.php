<?php

namespace App\Infrastructure\Database\Eloquent\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Domain\Common\DataTransferObjects\BaseDto;

interface RepositoryEloquentInterface
{
    public function create(BaseDto $dto): ?Model;

    public function update(int $modelId, BaseDto $dto): Model;

    public function deleteById(int $modelId): bool;

    public function restoreById(int $modelId): bool;

    public function permanentlyDeleteById(int $modelId): bool;

    public function getAll(array $columns = ['*']): Collection;

    public function getAllTrashed(array $columns = ['*']): Collection;

    public function getByField(string $field, mixed $value, array $columns = ['*']): Collection;

    public function firstById(int $modelId, array $columns = ['*'], array $relations = []): ?Model;

    public function firstTrashedById(int $modelId): ?Model;

    public function firstOnlyTrashedById(int $modelId): ?Model;

    public function firstByField(string $field, mixed $value, array $columns = ['*'], array $relations = []): ?Model;

    public function firstWhere(array $where, array $columns = ['*'], array $relations = []): ?Model;
}
