<?php

namespace Weather\Tests\Domain\Model\Exception;

use PHPUnit\Framework\TestCase;
use Weather\Domain\Model\Exceptions\BaseException;

class BaseExceptionTest extends TestCase
{
    protected string $exceptionClass;
    protected string $exceptionType;

    public function setUp(): void
    {
        $this->exceptionClass = BaseException::class;
        $this->exceptionType = "exception";
    }

    public function testCreate(): void
    {
        $error = new ($this->exceptionClass)("no");
        $this->assertInstanceOf(BaseException::class, $error);
        $this->assertEquals($this->exceptionType, $error->getType());
        $this->assertEquals(0, $error->getCode());
    }
}
