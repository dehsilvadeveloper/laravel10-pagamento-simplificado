<?php

namespace App\Domain\Auth\Exceptions;

use Exception;
use Illuminate\Http\Response;

class IncorrectEmailException extends Exception
{
    protected $message = 'Could not found a valid API user with the provided email.';
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
