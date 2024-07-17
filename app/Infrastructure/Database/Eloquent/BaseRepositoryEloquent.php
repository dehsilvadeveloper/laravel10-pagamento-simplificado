<?php

namespace App\Infrastructure\Database\Eloquent;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Response;
use App\Domain\Common\DataTransferObjects\BaseDto;
use App\Infrastructure\Database\Eloquent\Interfaces\RepositoryEloquentInterface;

class BaseRepositoryEloquent implements RepositoryEloquentInterface
{
    /** @var Model|EloquentBuilder|QueryBuilder|SoftDeletes */
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(BaseDto $dto): ?Model
    {
        $data = $dto->toArray();

        if (empty($data)) {
            throw new InvalidArgumentException(
                'You did not provide any data to create the record.',
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->model->create($data);
    }

    public function update(int $modelId, BaseDto $dto): Model
    {
        $data = $dto->toArray();

        if (empty($data)) {
            throw new InvalidArgumentException(
                'You did not provide any data to update the record.',
                Response::HTTP_BAD_REQUEST
            );
        }

        $item = $this->model->findOrFail($modelId);
        $item->update($data);
        $item->refresh();

        return $item;
    }

    public function deleteById(int $modelId): bool
    {
        $item = $this->model->findOrFail($modelId);

        return $item->delete();
    }

    public function restoreById(int $modelId): bool
    {
        $item = $this->model->onlyTrashed()->findOrFail($modelId);

        return $item->restore();
    }

    public function permanentlyDeleteById(int $modelId): bool
    {
        $item = $this->model->withTrashed()->findOrFail($modelId);

        return $item->forceDelete();
    }

    public function getAll(array $columns = ['*']): Collection
    {
        return $this->model->orderBy('id', 'desc')->get($columns);
    }

    public function getAllTrashed(array $columns = ['*']): Collection
    {
        return $this->model->onlyTrashed()->get($columns);
    }

    public function getByField(string $field, mixed $value, array $columns = ['*']): Collection
    {
        return $this->model->where($field, '=', $value)->get($columns);
    }

    public function firstById(int $modelId, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->select($columns)->with($relations)->find($modelId);
    }

    public function firstTrashedById(int $modelId): ?Model
    {
        return $this->model->withTrashed()->find($modelId);
    }

    public function firstOnlyTrashedById(int $modelId): ?Model
    {
        return $this->model->onlyTrashed()->find($modelId);
    }

    public function firstByField(string $field, mixed $value, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->select($columns)->with($relations)->where($field, '=', $value)->first();
    }

    public function firstWhere(array $where, array $columns = ['*'], array $relations = []): ?Model
    {
        return $this->model->select($columns)->with($relations)->where($where)->first();
    }
}
