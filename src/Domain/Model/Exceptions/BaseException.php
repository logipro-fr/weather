<?php

namespace Weather\Domain\Model\Exceptions;

use Exception;
use stdClass;

class BaseException extends Exception
{
    protected string $type = "exception";

    public function __construct(
        string $message = "", 
        int $code = 0, 
        private mixed $dataString = null, 
        ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getData(): mixed{
        return $this->dataString;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
