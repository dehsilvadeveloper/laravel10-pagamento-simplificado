<?php

namespace App\Domain\Transfer\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InvalidPayerException extends Exception
{
    protected $message = 'The payer is not valid.';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct(?string $message = null, ?int $code = null)
    {
        parent::__construct(
            (!$message) ? $this->message : $message,
            (!$code) ? $this->code : $code
        );
    }
}
