<?php

namespace Weather\Tests\WeatherStack\Domain\Model\CurrentWeather;

use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

use function Safe\file_get_contents;

class CurrentWeatherTest extends TestCase
{
    public function testCreate(): void
    {
        $jsonCurrent = file_get_contents(__DIR__ . '/resources/48.863,2.313.json');
        $requestedAt = new LocationTime(
            48.863,
            2.313,
            DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2023-06-21 16:14:32")
        );
        $current = new CurrentWeather(new CurrentWeatherId(), $requestedAt, $jsonCurrent);

        $this->assertInstanceOf(CurrentWeather::class, $current);
        $this->assertInstanceOf(CurrentWeatherId::class, $current->getId());

        $this->assertEquals(
            new LocationTime(48.863, 2.313, DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2023-06-21 16:14:32")),
            $current->getRequestAt()
        );

        $this->assertEquals("02:14 PM", $current->getCurrent('observation_time'));
        $this->assertEquals(27, $current->getCurrent('temperature'));
        $this->assertEquals("02:14 PM", $current->getCurrent('observation_time'));
        $this->assertEquals(27, $current->getCurrent('temperature'));
        $this->assertEquals(113, $current->getCurrent('weather_code'));
        $this->assertEquals(
            ["https://cdn.worldweatheronline.com/images/wsymbols01_png_64/wsymbol_0001_sunny.png"],
            $current->getCurrent('weather_icons')
        );
        $this->assertEquals([ "Sunny"], $current->getCurrent('weather_descriptions'));
        $this->assertEquals(4, $current->getCurrent('wind_speed'));
        $this->assertEquals(309, $current->getCurrent('wind_degree'));
        $this->assertEquals("NW", $current->getCurrent('wind_dir'));
        $this->assertEquals(1018, $current->getCurrent('pressure'));
        $this->assertEquals(0, $current->getCurrent('precip'));
        $this->assertEquals(48, $current->getCurrent('humidity'));
        $this->assertEquals(0, $current->getCurrent('cloudcover'));
        $this->assertEquals(27, $current->getCurrent('feelslike'));
        $this->assertEquals(7, $current->getCurrent('uv_index'));
        $this->assertEquals(10, $current->getCurrent('visibility'));
    }
}
