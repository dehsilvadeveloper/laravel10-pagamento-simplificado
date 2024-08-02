<?php

namespace App\Infrastructure\Database\Eloquent;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use App\Domain\Wallet\DataTransferObjects\CreateWalletDto;
use App\Domain\Wallet\Models\Wallet;
use App\Domain\Wallet\Repositories\WalletRepositoryInterface;

class WalletRepositoryEloquent implements WalletRepositoryInterface
{
    /** @var Model|EloquentBuilder */
    protected Model $model;

    public function __construct(Wallet $model)
    {
        $this->model = $model;
    }

    public function create(CreateWalletDto $dto): Wallet
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

    public function incrementById(int $id, string $column, float|int $amount): Wallet
    {
        $item = $this->model->findOrFail($id);
        $item->increment($column, $amount);
        $item->refresh();

        return $item;
    }

    public function decrementById(int $id, string $column, float|int $amount): Wallet
    {
        $item = $this->model->findOrFail($id);
        $item->decrement($column, $amount);
        $item->refresh();

        return $item;
    }
}
