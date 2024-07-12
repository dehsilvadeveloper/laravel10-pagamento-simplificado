<?php

namespace App\Domain\DocumentType\Exceptions;

use Exception;
use Illuminate\Http\Response;

class DocumentTypeNotFoundException extends Exception
{
    protected $message = 'The document type could not be found.';
    protected $code = Response::HTTP_NOT_FOUND;

    public function __construct()
    {
        parent::__construct($this->message, $this->code);
    }
}
