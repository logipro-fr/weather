<?php

namespace Weather\Domain\Model\Exceptions;

use Exception;

class BaseException extends Exception
{
    protected string $type = "unknown_weather";

    public function getType(): string
    {
        return $this->type;
    }
}
