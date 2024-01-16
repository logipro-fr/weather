<?php

namespace Weather\Tests\WeatherStack\Domain\Model;

use Weather\WeatherStack\Domain\Model\Exceptions\OutOfRangeDayHourException;
use Weather\WeatherStack\Domain\Model\HistoricalHour;
use PHPUnit\Framework\TestCase;

class HistoricalDayTest extends TestCase
{
    public function testCreate(): void
    {
        $weather = HistoricalDayBuilder::aHistoricalDay()->build();

        $this->assertEquals("2023-01-25 00:00", $weather->getHistoricalDate()->format("Y-m-d H:i"));
        //$this->assertEquals(???, $weather->getSunOn());
        $this->assertEquals(21, $weather->getMoonIllumination());
        $this->assertEquals(0, $weather->getTotalSnow());
        $this->assertEquals(8.7, $weather->getSunHour());
        $this->assertEquals("05:43 PM", $weather->getSunSet());
        $this->assertEquals("08:07 AM", $weather->getSunRise());
    }

    public function testMakeHistoricalHour(): void
    {
        $h = $this->makeHistoricalHour(10);
        $this->assertEquals(3, $h->getTemperature());

        $h = $this->makeHistoricalHour(14);
        $this->assertEquals(8, $h->getTemperature());
    }

    private function makeHistoricalHour(int $hour): HistoricalHour
    {
        $w = HistoricalDayBuilder::aHistoricalDay()->build();
        return $w->makeHistoricalHour($hour);
    }

    public function testOutOfRangeDayHourExceptionNegativeValue(): void
    {
        $this->expectException(OutOfRangeDayHourException::class);
        $this->expectExceptionMessage("Day hour must be a integer value between 0-23. '-1' is out of range.");
        $h = $this->makeHistoricalHour(-1);
    }

    public function testOutOfRangeDayHourExceptionGreaterThan23Value(): void
    {
        $this->expectException(OutOfRangeDayHourException::class);
        $this->expectExceptionMessage("Day hour must be a integer value between 0-23. '24' is out of range.");
        $h = $this->makeHistoricalHour(24);
    }
}
