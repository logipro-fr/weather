<?php

namespace Weather\Domain\Model\Exceptions;

class ApiException extends BaseException
{
    protected string $type = "API_connectivity_exception";
}
