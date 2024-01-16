<?php

namespace Weather\Tests\WeatherStack\Domain\Model;

use PHPUnit\Framework\TestCase;
use Weather\Share\Domain\Point;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Safe\DateTimeImmutable;

class HistoricalDayIdTest extends TestCase
{
    public function testNew(): void
    {
        $date =  DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-01-25 10:28");
        $id = new HistoricalDayId($location = new Point(44.039, 4.348), $date);
        $this->assertInstanceOf(HistoricalDayId::class, $id);

        $this->assertEquals("2023-01-25 44.039 4.348", $id->__toString());
        $this->assertEquals("2023-01-25 00:00", $id->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals($location, $id->getPoint());
    }

    public function testEquals(): void
    {
        $date1 =  DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-01-25 10:23");
        $id1 = new HistoricalDayId($location1 = new Point(46.841801, -0.492966), $date1);
        $date2 =  DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-01-25 14:23");
        $id2 = new HistoricalDayId($location2 = new Point(46.841801, -0.492966), $date2);

        $this->assertTrue($id1->equals($id2));

        $this->assertEquals("2023-01-25 00:00", $id1->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals($location1, $id1->getPoint());

        $this->assertEquals("2023-01-25 00:00", $id2->getHistoricalDate()->format("Y-m-d H:i"));
        $this->assertEquals($location2, $id2->getPoint());
    }
}
