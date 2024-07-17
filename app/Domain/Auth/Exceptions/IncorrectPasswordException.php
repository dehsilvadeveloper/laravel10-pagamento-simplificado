<?php

namespace App\Domain\Auth\Exceptions;

use Exception;
use Illuminate\Http\Response;

class IncorrectPasswordException extends Exception
{
    protected $message = 'The password provided for this API user is incorrect.';
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
