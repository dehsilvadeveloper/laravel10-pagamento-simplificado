<?php

namespace App\Domain\Transfer\Exceptions;

use Exception;
use Illuminate\Http\Response;

class TransferFailedException extends Exception
{
    protected $message = 'The transfer between the users has failed.';
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct(?string $message = null, ?int $code = null)
    {
        parent::__construct(
            (!$message) ? $this->message : $message,
            (!$code) ? $this->code : $code
        );
    }
}
