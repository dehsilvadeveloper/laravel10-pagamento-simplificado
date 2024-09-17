<?php

namespace App\Domain\Transfer\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UnauthorizedTransferException extends Exception
{
    protected $message = 'The transfer was not authorized.';
    protected $code = Response::HTTP_FORBIDDEN;

    public function __construct(?string $message = null, ?int $code = null)
    {
        parent::__construct(
            (!$message) ? $this->message : $message,
            (!$code) ? $this->code : $code
        );
    }
}
