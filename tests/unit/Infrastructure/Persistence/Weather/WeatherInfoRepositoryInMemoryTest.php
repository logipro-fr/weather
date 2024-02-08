<?php

namespace Weather\Tests\Infrastructure\Persistence\Weather;

use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class WeatherInfoRepositoryInMemoryTest extends TestCase
{
    protected WeatherInfoRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = new WeatherInfoRepositoryInMemory();
    }

    public function testAdd(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);

        $this->repository->save($info);
        $info_returned = $this->repository->findById($info->getId());
        $this->assertEquals($info, $info_returned);
    }

    public function testAddTwo(): void
    {
        $pointa = new Point(0, 0);
        $pointb = new Point(0, 0);
        $datea = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $dateb = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 13:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $infoa = new WeatherInfo($pointa, $datea, "{\"weather\":\"great\"}", Source::DEBUG);
        $infob = new WeatherInfo($pointb, $dateb, "{\"weather\":\"bad\"}", Source::DEBUG);

        $this->repository->save($infoa);
        $this->repository->save($infob);
        $info_returned = $this->repository->findById($infob->getId());
        $this->assertEquals($infob, $info_returned);
    }

    public function testFindByID(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $returned = $this->repository->findById($info->getId());

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFind(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);
        $id = new WeatherInfoId();
        $this->expectExceptionMessage("Object WeatherInfo of ID \"" . $id . "\" not found");
        $this->expectExceptionCode(404);

        $this->repository->findById($id);
    }

    public function testDoesNotFindFilled(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $infoa = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $infob = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $infoc = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $infod = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($infoa);
        $this->repository->save($infob);
        $this->repository->save($infoc);
        $this->repository->save($infod);

        $this->expectException(WeatherInfoNotFoundException::class);
        $this->expectExceptionCode(404);
        $this->repository->findById(new WeatherInfoId());
    }

    public function testFindFromDateAndPoint(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $returned = $this->repository->findByDateAndPoint($point, $date);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testFindFromDateAndPointHistorical(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG, true);
        $this->repository->save($info);

        $returned = $this->repository->findByDateAndPoint($point, $date, true);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testFindFromDateAndPointMicro(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $returned = $this->repository->findByDateAndPoint($point, $date);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFindByDateAndPoint(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);

        $pointA = new Point(0, 0);
        $dateA = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $pointB = new Point(1, 1);
        $dateB = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );

        $this->expectExceptionMessage("WeatherInfo of point \"" .
        $pointB . "\" at date " . $dateB->format("Y-m-d H:i:s.u") . " not found");
        $this->expectExceptionCode(404);

        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $this->repository->findByDateAndPoint($pointB, $dateB);
    }

    public function testFindFromImpreciseDateAndPoint(): void
    {
        $pointA = new Point(0.123, 4.621);
        $pointB = new Point(0.124, 4.620);
        $dateA = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $dateB = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:10:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $returned = $this->repository->findCloseByDateAndPoint($pointB, $dateB);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testFindFromImpreciseDateAndPointHistorical(): void
    {
        $pointA = new Point(0.123, 4.621);
        $pointB = new Point(0.124, 4.620);
        $dateA = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $dateB = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:10:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}", Source::DEBUG, true);
        $this->repository->save($info);

        $returned = $this->repository->findCloseByDateAndPoint($pointB, $dateB, true);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFindByImpreciseDateAndPoint(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);

        $pointA = new Point(0, 0);
        $dateA = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );
        $pointB = new Point(1, 1);
        $dateB = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:50:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );

        $this->expectExceptionMessage("WeatherInfo of point \"" .
        $pointB . "\" at date " . $dateB->format("Y-m-d H:i:s.u") . " not found");
        $this->expectExceptionCode(404);

        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $this->repository->findCloseByDateAndPoint($pointB, $dateB);
    }

    public function testDoesNotFindByImpreciseDateAndPointHistorical(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);

        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );

        $this->expectExceptionMessage("Historical WeatherInfo of point \"" .
        $point . "\" at date " . $date->format("Y-m-d H:i:s.u") . " not found");
        $this->expectExceptionCode(404);

        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);

        $this->repository->findCloseByDateAndPoint($point, $date, true);
    }

    public function testDoesNotFindByDateAndPointHistorical(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);

        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d H:i:s.u",
            "2024-01-01 12:00:01.012345",
            new DateTimeZone(date_default_timezone_get())
        );

        $this->expectExceptionMessage("Historical WeatherInfo of point \"" .
        $point . "\" at date " . $date->format("Y-m-d H:i:s.u") . " not found");

        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}", Source::DEBUG);
        $this->repository->save($info);
        $this->expectExceptionCode(404);

        $this->repository->findByDateAndPoint($point, $date, true);
    }
}
