<?php

namespace Weather\Tests\Domain\Model\Exception;

use Weather\Domain\Model\Exceptions\ApiException;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;

class WeatherInfoNotFoundExceptionTest extends BaseExceptionTest
{
    protected string $exceptionClass;
    protected string $exceptionType;

    public function setUp(): void
    {
        $this->exceptionClass = WeatherInfoNotFoundException::class;
        $this->exceptionType = "weatherinfo_not_found_exception";
    }
}
