<?php

namespace Weather\Domain\Model\Exceptions;

use Exception;

class DatabaseErrorException extends BaseException
{
    protected string $type = "database_error_exception";
}
