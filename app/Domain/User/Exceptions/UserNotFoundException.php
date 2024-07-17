<?php

namespace App\Domain\User\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UserNotFoundException extends Exception
{
    protected $message = 'The user could not be found.';
    protected $code = Response::HTTP_NOT_FOUND;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
