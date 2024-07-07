<?php

namespace App\Infrastructure\Database\Eloquent;

use App\Infrastructure\Database\Eloquent\BaseRepositoryEloquent;
use App\Domain\DocumentType\Models\DocumentType;
use App\Domain\DocumentType\Repositories\DocumentTypeRepositoryInterface;

class DocumentTypeRepositoryEloquent extends BaseRepositoryEloquent implements DocumentTypeRepositoryInterface
{
    public function __construct(DocumentType $model)
    {
        $this->model = $model;
    }
}
