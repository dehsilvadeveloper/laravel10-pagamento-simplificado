<?php

namespace App\Infrastructure\Database\Eloquent;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use App\Domain\TransferAuthorization\DataTransferObjects\CreateTransferAuthorizationResponseDto;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;
use App\Domain\TransferAuthorization\Repositories\TransferAuthorizationResponseRepositoryInterface;

class TransferAuthorizationResponseRepositoryEloquent implements TransferAuthorizationResponseRepositoryInterface
{
    /** @var Model|EloquentBuilder */
    protected Model $model;

    public function __construct(TransferAuthorizationResponse $model)
    {
        $this->model = $model;
    }

    public function create(CreateTransferAuthorizationResponseDto $dto): TransferAuthorizationResponse
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
}
