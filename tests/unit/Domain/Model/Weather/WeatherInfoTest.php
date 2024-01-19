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
}
