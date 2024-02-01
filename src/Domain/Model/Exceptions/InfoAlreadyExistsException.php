<?php

namespace Weather\Domain\Model\Exceptions;

use Exception;

class InfoAlreadyExistsException extends Exception
{
    protected string $type = "existing_info_exception";
}
