<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\Doctrine\Types;

use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Weather\WeatherStack\Infrastructure\Persistence\Doctrine\Types\CurrentWeatherIdType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;

class CurrentWeatherIdTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('current_weather_id', (new CurrentWeatherIdType())->getName());
    }

    public function testConvertToPHPValue(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new CurrentWeatherIdType();
        $id = $type->convertToPHPValue("current_weather", $platform);
        $this->assertEquals(true, $id instanceof CurrentWeatherId);
    }

    public function testConvertToDatabaseValue(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new CurrentWeatherIdType();
        $dbValue = $type->convertToDatabaseValue($id = new CurrentWeatherId(), $platform);
        $this->assertEquals($id->__toString(), $dbValue);
    }
}
