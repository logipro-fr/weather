<?php

namespace Weather\Tests\Domain\Model\Weather;

use Phariscope\Event\EventPublisher;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Event\WeatherInfoCreated;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Tests\Domain\TestSubscriber;

class WeatherInfoTest extends TestCase
{
    public function testCreate(): void
    {
        $point = new Point(0, 0);
        $createdDate = new DateTimeImmutable("2020-01-01 12:00");
        $data = "{}";
        $info = new WeatherInfo($point, $createdDate, $data);
        $info2 = new WeatherInfo($point, $createdDate, $data, true);

        $this->assertEquals($point, $info->getPoint());
        $this->assertEquals($createdDate, $info->getDate());
        $this->assertEquals($data, $info->getData());
        $this->assertEquals(false, $info->isHistorical());
        $this->assertEquals(true, $info2->isHistorical());
        $this->assertInstanceOf(WeatherInfoId::class, $info->getId());
    }

    public function testEvent(): void
    {
        $spy = new TestSubscriber();

        EventPublisher::instance()->subscribe($spy);

        $point = new Point(0, 0);
        $createdDate = new DateTimeImmutable("2020-01-01 12:00");
        $data = "{}";
        $info = new WeatherInfo($point, $createdDate, $data);

        EventPublisher::instance()->distribute();
        $event = $spy->domainEvent;
        $this->assertInstanceOf(WeatherInfoCreated::class, $event);
        /** @var WeatherInfoCreated $event */
        $this->assertEquals((string)$info->getId(), $event->id);
    }

    public function testCloseTo(): void
    {
        $pointA = new Point(0.01, 0.05);
        $dateA = new DateTimeImmutable("2020-01-01 12:00");
        $data = "{}";
        $info = new WeatherInfo($pointA, $dateA, $data);

        $pointB = new Point(0.01 + 0.05, 0.0);
        $dateB = new DateTimeImmutable("2020-01-01 12:30");

        $this->assertTrue($info->closeTo($pointB, $dateB));
    }

    public function testNotCloseToLat(): void
    {
        $pointA = new Point(1.01, 0.05);
        $dateA = new DateTimeImmutable("2020-01-01 12:00");
        $data = "{}";
        $info = new WeatherInfo($pointA, $dateA, $data);

        $pointB = new Point(0.008, 0.053);
        $dateB = new DateTimeImmutable("2020-01-01 12:15");

        $this->assertFalse($info->closeTo($pointB, $dateB));
    }

    public function testNotCloseToLong(): void
    {
        $pointA = new Point(0.01, -1.05);
        $dateA = new DateTimeImmutable("2020-01-01 12:00");
        $data = "{}";
        $info = new WeatherInfo($pointA, $dateA, $data);

        $pointB = new Point(0.008, 0.053);
        $dateB = new DateTimeImmutable("2020-01-01 12:15");

        $this->assertFalse($info->closeTo($pointB, $dateB));
    }

    public function testNotCloseToTime(): void
    {
        $pointA = new Point(0.01, 0.05);
        $dateA = new DateTimeImmutable("2020-01-01 12:00");
        $data = "{}";
        $info = new WeatherInfo($pointA, $dateA, $data);

        $pointB = new Point(0.008, 0.053);
        $dateB = new DateTimeImmutable("2020-01-01 13:00");

        $this->assertFalse($info->closeTo($pointB, $dateB));
    }

    public function testJsonSerialize(): void
    {
        $point = new Point(1.256, 5.156);
        $date = new DateTimeImmutable("2020-01-01 13:00");
        $historical = true;
        $result = '{"this":"is","ok!":5}';
        $info = new WeatherInfo($point, $date, $result, $historical);
        $target = [
            "id" => $info->getId(),
            "latitude" => $point->getLatitude(),
            "longitude" => $point->getLongitude(),
            "date" => $date->format("Y-m-d H:i:s.u"),
            "historical" => $historical,
            "result" => json_decode($result)
        ];

        $this->assertEquals($target, $info->jsonSerialize());
    }
}
