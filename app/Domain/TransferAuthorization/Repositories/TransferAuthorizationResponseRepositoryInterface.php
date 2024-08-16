<?php

namespace App\Domain\TransferAuthorization\Repositories;

use App\Domain\TransferAuthorization\DataTransferObjects\CreateTransferAuthorizationResponseDto;
use App\Domain\TransferAuthorization\Models\TransferAuthorizationResponse;

interface TransferAuthorizationResponseRepositoryInterface
{
    public function create(CreateTransferAuthorizationResponseDto $dto): TransferAuthorizationResponse;
}
