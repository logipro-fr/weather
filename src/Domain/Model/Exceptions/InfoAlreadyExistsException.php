<?php

namespace Weather\Domain\Model\Exceptions;

class InfoAlreadyExistsException extends BaseException
{
    protected string $type = "existing_info_exception";
}
