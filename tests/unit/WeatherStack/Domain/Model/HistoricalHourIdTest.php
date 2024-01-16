<?php

namespace Weather\Tests\WeatherStack\Domain\Model;

use PHPUnit\Framework\TestCase;
use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use Safe\DateTimeImmutable;

class HistoricalHourIdTest extends TestCase
{
    public function testNew(): void
    {
        $locationTime =  $this->createLocationTime(44.039, 4.348, "2023-01-25 10:28");

        $id = new HistoricalHourId($locationTime);
        $this->assertInstanceOf(HistoricalHourId::class, $id);

        $this->assertEquals("2023-01-25 10 44.039 4.348", $id->__toString());
        $this->assertEquals("2023-01-25 10:00", $id->getHistoricalDate()->format("Y-m-d H:i"));

        $expectedLocationTime =  $this->createLocationTime(44.039, 4.348, "2023-01-25 10:00");
        $this->assertEquals($expectedLocationTime, $id->getLocationTime());
    }

    private function createLocationTime(float $latitude, float $longitude, string $date): LocationTime
    {
        return new LocationTime(
            $latitude,
            $longitude,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", $date)
        );
    }
    public function testEquals(): void
    {
        $locationTime1 =  $this->createLocationTime(46.841801, -0.492966, "2023-01-25 10:23");
        $id1 = new HistoricalHourId($locationTime1);
        $locationTime2 =  $this->createLocationTime(46.841801, -0.492966, "2023-01-25 10:58");
        $id2 = new HistoricalHourId($locationTime2);

        $this->assertTrue($id1->equals($id2));

        $this->assertEquals("2023-01-25 10:00", $id1->getHistoricalDate()->format("Y-m-d H:i"));

        $this->assertEquals("2023-01-25 10:00", $id2->getHistoricalDate()->format("Y-m-d H:i"));
    }
}
