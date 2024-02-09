<?php

namespace Weather\Domain\Model\Exceptions;

class InvalidArgumentException extends BaseException
{
    protected string $type = "invalid_argument";
}
