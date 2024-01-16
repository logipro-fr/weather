<?php

namespace Weather\Share\Domain;

use DateInterval;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class LocationTimeTest extends TestCase
{
    public function testCreationAndGetters(): void
    {
        $time = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-14 14:00");
        $locationTime = new LocationTime(
            45,
            12.2,
            $time
        );

        $this->assertEquals(45, $locationTime->getLatitude());
        $this->assertEquals(12.2, $locationTime->getLongitude());
        $this->assertEquals(new Point(45, 12.2), $locationTime->getPoint());
        $this->assertEquals($time, $locationTime->getTime());
    }

    public function testEquals(): void
    {
        $time = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-14 14:00");
        $locationTime = new LocationTime(45.5, 12.2, $time);
        $this->assertTrue($locationTime->equals(new LocationTime(45.5, 12.2, $time)));
        $this->assertFalse($locationTime->equals(new LocationTime(45.5, 12.3, $time)));
        $this->assertFalse($locationTime->equals(new LocationTime(45.6, 12.2, $time)));
        $this->assertFalse(
            $locationTime->equals(
                new LocationTime(45.5, 12.2, $time->add(DateInterval::createFromDateString("1 hour")))
            )
        );
    }

    public function testToString(): void
    {
        $time = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-14 14:00");
        $locationTime = new LocationTime(45.5, 12.2, $time);
        $this->assertEquals(
            "45.5 12.2 2023-12-14 14:00:00",
            $locationTime->__toString()
        );
    }

    public function testTimePreciseness(): void
    {
        $time = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-14 14:00");
        $timeALittleAfter = $time->add(DateInterval::createFromDateString("10 microseconds"));

        $locationTime = new LocationTime(45.5, 12.2, $time);
        $locationTimeALittleAfter = new LocationTime(45.5, 12.2, $timeALittleAfter);

        $this->assertEquals($locationTime->getTime(), $locationTimeALittleAfter->getTime());
    }

    public function testExampleThatDidNotWork(): void
    {
        $loc1 = new LocationTime(
            45.897971,
            6.281219,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-03 15:01")
        );
        $loc2 = new LocationTime(
            45.898474,
            6.283668,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-03 14:49")
        );
        $this->assertTrue($loc1->isSimilarTo($loc2));
    }

    public function testSimilarity(): void
    {
        $loc1 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 13:30")
        );
        $loc2 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 13:30")
        );
        $this->assertTrue($loc1->isSimilarTo($loc2));
        $this->assertTrue($loc1->isSimilarTo($loc2, 0, 0));

        $loc3 = new LocationTime(
            45.001,
            2.001,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 13:30")
        );
        $this->assertTrue($loc1->isSimilarTo($loc3));
        $this->assertFalse($loc1->isSimilarTo($loc3, 0, 30));
        $loc4 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 13:45")
        );
        $this->assertTrue($loc1->isSimilarTo($loc4));
        $this->assertFalse($loc1->isSimilarTo($loc4, 1000, 0));

        $loc5 = new LocationTime(
            46,
            3,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 13:30")
        );
        $this->assertFalse($loc1->isSimilarTo($loc5));
        $this->assertTrue($loc1->isSimilarTo($loc5, 1000000, 30));
        $loc6 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 14:15")
        );
        $this->assertFalse($loc1->isSimilarTo($loc6));
        $this->assertTrue($loc1->isSimilarTo($loc6, 1000, 60));
    }

    public function testHourSimilarity(): void
    {
        $loc1 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 14:00")
        );
        $loc2 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 15:00")
        );
        $this->assertTrue($loc1->isSimilarOnNextHourTo($loc2));

        $loc3 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 15:01")
        );
        $this->assertFalse($loc1->isSimilarOnNextHourTo($loc3));

        $loc4 = new LocationTime(
            45,
            2,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-12-13 13:30")
        );
        $this->assertFalse($loc1->isSimilarOnNextHourTo($loc4));
    }
}
