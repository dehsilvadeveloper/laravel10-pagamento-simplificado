<?php

namespace App\Domain\Common\Exceptions;

use Exception;
use Illuminate\Http\Response;

class EmptyRequestException extends Exception
{
    protected $message = 'You cannot process a resource without provide data.';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct(?string $message = null, ?int $code = null)
    {
        parent::__construct(
            (!$message) ? $this->message : $message,
            (!$code) ? $this->code : $code
        );
    }
}
