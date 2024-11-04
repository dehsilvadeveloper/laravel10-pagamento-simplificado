<?php

namespace App\Infrastructure\Database\Eloquent;

use App\Domain\User\Models\UserType;
use App\Domain\User\Repositories\UserTypeRepositoryInterface;

class UserTypeRepositoryEloquent extends BaseRepositoryEloquent implements UserTypeRepositoryInterface
{
    public function __construct(UserType $model)
    {
        $this->model = $model;
    }
}
