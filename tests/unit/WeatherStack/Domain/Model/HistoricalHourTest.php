<?php

namespace Weather\Tests\WeatherStack\Domain\Model;

use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Domain\Model\HistoricalHour;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

use function Safe\file_get_contents;

class HistoricalHourTest extends TestCase
{
    public function testCreate(): void
    {
        $weather = $this->makeWeather("2023-01-25 10");

        $this->assertEquals("2023-01-25 10:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals(21, $weather->getMoonIllumination());
        $this->assertEquals(21, $weather->get('moon_illumination'));
        $this->assertEquals(0, $weather->getTotalSnow());
        $this->assertEquals(0, $weather->get('totalsnow'));
        $this->assertEquals(8.7, $weather->getSunHour());
        $this->assertEquals(8.7, $weather->get('sunhour'));

        $this->assertEquals(true, $weather->getSunOn());

        $this->assertEquals(3, $weather->getTemperature());
        $this->assertEquals(3, $weather->get('temperature'));
        $this->assertEquals(13, $weather->getWindSpeed());
        $this->assertEquals(13, $weather->get('wind_speed'));
        $this->assertEquals(117, $weather->getWindDegree());
        $this->assertEquals(117, $weather->get('wind_degree'));
        $this->assertEquals("ESE", $weather->getWindDir());
        $this->assertEquals(113, $weather->getWeatherCode());
        $this->assertEquals(
            ["https://cdn.worldweatheronline.com/images/wsymbols01_png_64/wsymbol_0001_sunny.png"],
            $weather->getWeatherIcons()
        );
        $this->assertEquals(["Sunny"], $weather->getWeatherDescriptions());
        $this->assertEquals(0, $weather->getPrecip());
        $this->assertEquals(70, $weather->getHumidity());
        $this->assertEquals(10, $weather->getVisibility());
        $this->assertEquals(1026, $weather->getPressure());
        $this->assertEquals(17, $weather->getCloudCover());
        $this->assertEquals(3, $weather->getHeatIndex());
        $this->assertEquals(-2, $weather->getDewPoint());
        $this->assertEquals(-1, $weather->getiWndChill());
        $this->assertEquals(25, $weather->getWindGust());
        $this->assertEquals(-1, $weather->getfeelslike());
        $this->assertEquals(0, $weather->getChanceOfRain());
        $this->assertEquals(0, $weather->getChanceOfRemdry());
        $this->assertEquals(0, $weather->getChanceOfWindy());
        $this->assertEquals(0, $weather->getchanceofovercast());
        $this->assertEquals(0, $weather->getChanceOfSunshine());
        $this->assertEquals(0, $weather->getChanceOfFrost());
        $this->assertEquals(0, $weather->getChanceOfHighTemp());
        $this->assertEquals(0, $weather->getChanceOfFog());
        $this->assertEquals(0, $weather->getChanceOfSnow());
        $this->assertEquals(0, $weather->getchanceofthunder());
        $this->assertEquals(2, $weather->getUvIndex());
    }

    private function makeWeather(string $date): HistoricalHour
    {
        $content = file_get_contents(__DIR__ . '/resources/44.039,4.348.json');
        $historicalDate = DateTimeImmutable::createFromFormat("Y-m-d H", $date);
        $id = new HistoricalHourId($lt = new LocationTime(44.039, 4.348, $historicalDate));

        $day = new HistoricalDay(new HistoricalDayId($lt->getPoint(), $historicalDate), $content);
        return new HistoricalHour(
            $id,
            $day
        );
    }

    public function testSunOn(): void
    {
        $weather = $this->makeHistoricalHourSun(9);
        $this->assertEquals("2023-01-25 09:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertTrue((bool)$weather->getSunOn());

        $weather = $this->makeHistoricalHourSun(17);
        $this->assertEquals("2023-01-25 17:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertTrue((bool)$weather->getSunOn());

        $weather = $this->makeHistoricalHourSun(8);
        $this->assertEquals("2023-01-25 08:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertFalse((bool)$weather->getSunOn());

        $weather = $this->makeHistoricalHourSun(18);
        $this->assertEquals("2023-01-25 18:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertFalse((bool)$weather->getSunOn());
    }

    private function makeHistoricalHourSun(
        int $hour,
        string $sunrise = "08:07 AM",
        string $sunset = "05:43 PM"
    ): HistoricalHour {
        return HistoricalHourBuilder::aHistoricalHour()
            ->withHour($hour)
            ->withParamSetTo('sunrise', $sunrise)
            ->withParamSetTo('sunset', $sunset)
            ->build();
    }


    public function testMoonOn(): void
    {
        $weather = $this->makeHistoricalHourMoon(9, "08:07 AM", "05:43 PM");
        $this->assertEquals("2023-01-25 09:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(17, "08:07 AM", "05:43 PM");
        $this->assertEquals("2023-01-25 17:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(8, "08:07 AM", "05:43 PM");
        $this->assertEquals("2023-01-25 08:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertFalse((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(18, "08:07 AM", "05:43 PM");
        $this->assertEquals("2023-01-25 18:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertFalse((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(15, "02:03 PM", "01:30 AM");
        $this->assertEquals("2023-01-25 15:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(1, "02:03 PM", "01:30 AM");
        $this->assertEquals("2023-01-25 01:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(2, "02:03 PM", "01:30 AM");
        $this->assertEquals("2023-01-25 02:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertFalse((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(13, "02:03 PM", "01:30 AM");
        $this->assertEquals("2023-01-25 13:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertFalse((bool)$weather->getMoonOn());
    }

    private function makeHistoricalHourMoon(
        int $hour,
        string $moonrise = "08:07 AM",
        string $moonset = "05:43 PM"
    ): HistoricalHour {
        return HistoricalHourBuilder::aHistoricalHour()
            ->withHour($hour)
            ->withParamSetTo('moonrise', $moonrise)
            ->withParamSetTo('moonset', $moonset)
            ->build();
    }

    public function testNoMoonSetOrRise(): void
    {
        $weather = $this->makeHistoricalHourMoon(0, "08:07 AM", "no moon set");
        $this->assertFalse((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(9, "08:07 AM", "no moon set");
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(23, "08:07 AM", "no moon set");
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(0, "no moon rise", "05:43 PM");
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(5, "no moon rise", "05:43 PM");
        $this->assertTrue((bool)$weather->getMoonOn());

        $weather = $this->makeHistoricalHourMoon(6, "no moon rise", "05:43 PM");
        $this->assertTrue((bool)$weather->getMoonOn());
    }
}
