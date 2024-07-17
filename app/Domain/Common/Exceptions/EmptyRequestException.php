<?php

namespace App\Domain\Common\Exceptions;

use Exception;
use Illuminate\Http\Response;

class EmptyRequestException extends Exception
{
    protected $message = 'You cannot update a resource without provide data.';
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
