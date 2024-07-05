<?php

namespace App\Domain\DocumentType\Services\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Domain\DocumentType\Models\DocumentType;

interface DocumentTypeServiceInterface
{
    public function firstById(int $id): ?DocumentType;

    public function getAll(array $columns = ['*']): Collection;
}
