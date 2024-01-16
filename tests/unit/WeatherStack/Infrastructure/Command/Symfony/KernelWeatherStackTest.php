<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Symfony;

use Weather\WeatherStack\Infrastructure\Command\Symfony\KernelWeatherStack;
use PHPUnit\Framework\TestCase;

class KernelWeatherStackTest extends TestCase
{
    public function testConstruct(): void
    {
        $kernel = new KernelWeatherStack("test", true);
        $this->assertInstanceOf(KernelWeatherStack::class, $kernel);
        $this->assertTrue($kernel->isDebug());
    }
}
