<?php

namespace App\Infrastructure\Database\Eloquent;

use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use App\Domain\Notification\DataTransferObjects\CreateNotificationDto;
use App\Domain\Notification\Models\Notification;
use App\Domain\Notification\Repositories\NotificationRepositoryInterface;

class NotificationRepositoryEloquent implements NotificationRepositoryInterface
{
    /** @var Model|EloquentBuilder */
    protected Model $model;

    public function __construct(Notification $model)
    {
        $this->model = $model;
    }

    public function create(CreateNotificationDto $dto): Notification
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
