<?php

namespace App\Domain\TransferAuthorization\Services\Interfaces;

use App\Domain\TransferAuthorization\DataTransferObjects\AuthorizeTransferDto;

interface TransferAuthorizerServiceInterface
{
    public function authorize(AuthorizeTransferDto $dto): bool;
}
