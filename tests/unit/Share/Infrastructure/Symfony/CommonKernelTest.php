<?php

namespace Weather\Tests\Share\Infrastructure\Symfony;

use Weather\Share\Infrastructure\Symfony\CommonKernel;
use PHPUnit\Framework\TestCase;

class CommonKernelTest extends TestCase
{
    public function testConstruct(): void
    {
        $kernel = new CommonKernel("test", true);
        $this->assertInstanceOf(CommonKernel::class, $kernel);
        $this->assertTrue($kernel->isDebug());
    }
}
