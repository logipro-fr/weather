<?php

namespace Weather\Tests\WeatherStack\Domain\Model\CurrentWeather;

use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use PHPUnit\Framework\TestCase;

class CurrentWeatherIdIdTest extends TestCase
{
    public function testNewRegisteredVisitorId(): void
    {
        $id = new CurrentWeatherId();
        $this->assertInstanceOf(CurrentWeatherId::class, $id);
    }

    public function testEquals(): void
    {
        $id = new CurrentWeatherId();
        $id2 = new CurrentWeatherId($id->getId());
        $this->assertTrue($id->equals($id2));
    }

    public function testToString(): void
    {
        $id = new CurrentWeatherId("unId");
        $this->assertSame("unId", $id->__toString());
    }
}
