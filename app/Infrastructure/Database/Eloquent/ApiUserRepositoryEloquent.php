<?php

namespace App\Infrastructure\Database\Eloquent;

use App\Domain\ApiUser\Models\ApiUser;
use App\Domain\ApiUser\Repositories\ApiUserRepositoryInterface;

class ApiUserRepositoryEloquent extends BaseRepositoryEloquent implements ApiUserRepositoryInterface
{
    public function __construct(ApiUser $model)
    {
        $this->model = $model;
    }
}
