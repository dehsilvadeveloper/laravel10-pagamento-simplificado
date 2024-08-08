<?php

namespace App\Domain\Common\Exceptions;

use Exception;
use Illuminate\Http\Response;

class EmptyResponseException extends Exception
{
    protected $message = 'The body of the response returned is empty.';
    protected $code = Response::HTTP_NO_CONTENT;

    public function __construct(?string $message = null, ?int $code = null)
    {
        parent::__construct(
            (!$message) ? $this->message : $message,
            (!$code) ? $this->code : $code
        );
    }
}
