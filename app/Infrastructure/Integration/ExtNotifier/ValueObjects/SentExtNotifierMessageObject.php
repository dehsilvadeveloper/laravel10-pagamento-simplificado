<?php

namespace App\Infrastructure\Integration\ExtNotifier\ValueObjects;

class SentExtNotifierMessageObject
{
    public function __construct(
        private string $recipient,
        private string $message,
        private string $status
    ) {
    }

    public function getRecipient(): string
    {
        return $this->recipient;
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
