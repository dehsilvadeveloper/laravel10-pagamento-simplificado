<?php

namespace App\Domain\Common\ValueObjects;

class SentSmsMessageObject
{
    public function __construct(
        private string $phoneNumber,
        private string $message,
        private string $status
    ) {
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
