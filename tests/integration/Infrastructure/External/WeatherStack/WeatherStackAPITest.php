<?php

namespace Weather\Tests\Infrastructure\External\WeatherStack;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Weather\Domain\Model\Exceptions\ApiException;
use Weather\Infrastructure\External\WeatherStack\WeatherStackAPI;

class WeatherStackAPITest extends TestCase
{

    public function testBrokenCurrent(): void
    {
        $this->expectException(ApiException::class);
        $reflector = new ReflectionClass(WeatherStackAPI::class);
        $method = $reflector->getMethod("currentQuery");
        $api = new WeatherStackAPI("void");
        $method->invokeArgs($api, ["1,1"]);
    }

    public function testBrokenHistorical(): void
    {
        $this->expectException(ApiException::class);
        $reflector = new ReflectionClass(WeatherStackAPI::class);
        $method = $reflector->getMethod("historicalQuery");
        $api = new WeatherStackAPI("void");
        $method->invokeArgs($api, ["1,1", "2024-01-01 12:20"]);
    }
}
