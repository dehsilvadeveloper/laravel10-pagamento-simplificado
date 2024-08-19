<?php

namespace App\Infrastructure\Database\Eloquent;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use App\Domain\Transfer\DataTransferObjects\CreateTransferDto;
use App\Domain\Transfer\Enums\TransferStatusEnum;
use App\Domain\Transfer\Models\Transfer;
use App\Domain\Transfer\Repositories\TransferRepositoryInterface;

class TransferRepositoryEloquent implements TransferRepositoryInterface
{
    /** @var Model|EloquentBuilder */
    protected Model $model;

    public function __construct(Transfer $model)
    {
        $this->model = $model;
    }

    public function create(CreateTransferDto $dto): Transfer
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

    public function updateStatus(int $id, TransferStatusEnum $newStatus): Transfer
    {
        $item = $this->model->findOrFail($id);
        $item->update([
            'transfer_status_id' => $newStatus->value
        ]);
        $item->refresh();

        return $item;
    }
}
