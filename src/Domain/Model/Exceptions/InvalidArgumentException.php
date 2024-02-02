<?php

namespace Weather\Domain\Model\Exceptions;

use Exception;

class InvalidArgumentException extends BaseException
{
    protected string $type = "invalid_argument";
}
