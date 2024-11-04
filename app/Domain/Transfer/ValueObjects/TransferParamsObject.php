<?php

namespace App\Domain\Transfer\ValueObjects;

use App\Domain\Transfer\Exceptions\InsufficientFundsException;
use App\Domain\Transfer\Exceptions\InvalidPayerException;
use App\Domain\Transfer\Exceptions\PayerNotFoundException;
use App\Domain\User\Enums\UserTypeEnum;
use App\Domain\User\Models\User;
use App\Domain\User\Repositories\UserRepositoryInterface;

class TransferParamsObject
{
    private UserRepositoryInterface $userRepository;
    private int $payerId;
    private int $payeeId;
    private float $amount;
    private ?User $payer;

    public function __construct(
        int $payerId,
        int $payeeId,
        float $amount
    ) {
        $this->userRepository = app(UserRepositoryInterface::class);

        $this->payerId = $payerId;
        $this->payeeId = $payeeId;
        $this->amount = $amount;

        $this->validate();
    }

    public function getPayerId(): int
    {
        return $this->payerId;
    }

    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getPayer(): User
    {
        return $this->payer;
    }

    public function toArray(): array
    {
        return [
            'payer_id' => $this->payerId,
            'payee_id' => $this->payeeId,
            'amount' => $this->amount
        ];
    }

    private function findPayerOnDatabase(): ?User
    {
        return $this->userRepository->firstById($this->payerId);
    }

    private function validate(): bool
    {
        return $this->validatePayer();
    }

    private function validatePayer(): bool
    {
        $this->payer = $this->findPayerOnDatabase();

        if (!$this->payer) {
            throw new PayerNotFoundException();
        }

        $this->validatePayerType($this->payer);
        $this->validatePayerWallet($this->payer, $this->amount);

        return true;
    }

    private function validatePayerType(User $payer): bool
    {
        if ($payer->user_type_id == UserTypeEnum::SHOPKEEPER->value) {
            throw new InvalidPayerException(
                message: 'The payer of a transfer cannot be of type shopkeeper.'
            );
        }

        return true;
    }

    private function validatePayerWallet(User $payer, float $amount): bool
    {
        if ($payer->wallet->balance < $amount) {
            throw new InsufficientFundsException(
                message: 'The payer does not have sufficient funds in his wallet for this operation.'
            );
        }

        return true;
    }
}
