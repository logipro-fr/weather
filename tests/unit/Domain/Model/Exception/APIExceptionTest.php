<?php

namespace Weather\Tests\Domain\Model\Exception;

use Weather\Domain\Model\Exceptions\ApiException;

class ApiExceptionTest extends BaseExceptionTest
{
    protected string $exceptionClass;
    protected string $exceptionType;

    public function setUp(): void
    {
        $this->exceptionClass = ApiException::class;
        $this->exceptionType = "API_connectivity_exception";
    }
}
