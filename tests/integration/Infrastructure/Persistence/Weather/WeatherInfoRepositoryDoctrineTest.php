<?php

namespace Weather\Tests\Infrastructure\External\WeatherStack;

use DoctrineTestingTools\DoctrineRepositoryTesterTrait;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryDoctrine;

use function PHPUnit\Framework\assertEquals;

class WeatherInfoRepositoryDoctrineTest extends TestCase
{
    use DoctrineRepositoryTesterTrait;

    private WeatherInfoRepositoryDoctrine $repos;

    public function setUp(): void
    {
        $this->initDoctrineTester();
        $this->clearTables(["weatherinfos"]);
        $this->repos = new WeatherInfoRepositoryDoctrine($this->getEntityManager());
    }

    public function testSecond(): void
    {
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-01 12:30:00.000000");
        $point = new Point(0, 1);
        $info = new WeatherInfo($point, $date, "{}", Source::DEBUG);

        $this->repos->save($info);
        $return = $this->repos->findByDateAndPoint($point, $date);

        assertEquals($info->getDate()->format("YmdHisu"), $return->getDate()->format("YmdHisu"));
        assertEquals($info->getPoint(), $return->getPoint());
    }

    public function testMillisecond(): void
    {
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-01 12:30:00.123000");
        $point = new Point(0, 1);
        $info = new WeatherInfo($point, $date, "{}", Source::DEBUG);

        $this->repos->save($info);
        $return = $this->repos->findByDateAndPoint($point, $date);

        assertEquals($info->getDate()->format("YmdHisu"), $return->getDate()->format("YmdHisu"));
        assertEquals($info->getPoint(), $return->getPoint());
    }

    public function testMicrosecond(): void
    {
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-01 12:30:00.123456");
        $point = new Point(0, 1);
        $info = new WeatherInfo($point, $date, "{}", Source::DEBUG);

        $this->repos->save($info);
        $return = $this->repos->findByDateAndPoint($point, $date);

        assertEquals($info->getDate()->format("YmdHisu"), $return->getDate()->format("YmdHisu"));
        assertEquals($info->getPoint(), $return->getPoint());
    }
}
