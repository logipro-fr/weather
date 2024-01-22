<?php

namespace Weather\Tests\Infrastructure\Persistence\Weather;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\InfoAlreadyExistsException;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class InfoRepositoryInMemoryTest extends TestCase
{
    public function testAdd(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");

        $repository->save($info);
        $info_returned = $repository->findById($info->getId());
        $this->assertEquals($info, $info_returned);
    }

    public function testAddTwo(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $pointa = new Point(0, 0);
        $pointb = new Point(0, 0);
        $datea = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $dateb = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 13:00");
        $infoa = new WeatherInfo($pointa, $datea, "{\"weather\":\"great\"}");
        $infob = new WeatherInfo($pointb, $dateb, "{\"weather\":\"bad\"}");

        $repository->save($infoa);
        $repository->save($infob);
        $info_returned = $repository->findById($infob->getId());
        $this->assertEquals($infob, $info_returned);
    }

    public function testFind(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $repository->save($info);

        $returned = $repository->findById($info->getId());

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFind(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);
        $id = new WeatherInfoId();
        $this->expectExceptionMessage("Object WeatherInfo of ID \"" . $id . "\" not found");
        $repository = new WeatherInfoRepositoryInMemory();

        $repository->findById($id);
    }

    public function testDoesNotFindFilled(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $infoa = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $infob = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $infoc = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $infod = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $repository->save($infoa);
        $repository->save($infob);
        $repository->save($infoc);
        $repository->save($infod);

        $this->expectException(WeatherInfoNotFoundException::class);
        $repository->findById(new WeatherInfoId());
    }

    public function testFindFromDateAndPoint(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $repository->save($info);

        $returned = $repository->findByDateAndPoint($point, $date);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFindByDateAndPoint(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);

        $repository = new WeatherInfoRepositoryInMemory();
        $pointA = new Point(0, 0);
        $dateA = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $pointB = new Point(1, 1);
        $dateB = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:50");

        $this->expectExceptionMessage("WeatherInfo of point \"" .
        $pointB . "\" at date " . $dateB->format("Y-m-d H:i:s") . " not found");

        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}");
        $repository->save($info);

        $returned = $repository->findByDateAndPoint($pointB, $dateB);
    }

    public function testFindFromImpreciseDateAndPoint(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $pointA = new Point(0.123, 4.621);
        $pointB = new Point(0.124, 4.619);
        $dateA = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $dateB = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:10");
        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}");
        $repository->save($info);

        $returned = $repository->findCloseByDateAndPoint($pointB, $dateB);

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFindByImpreciseDateAndPoint(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);

        $repository = new WeatherInfoRepositoryInMemory();
        $pointA = new Point(0, 0);
        $dateA = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $pointB = new Point(1, 1);
        $dateB = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:50");

        $this->expectExceptionMessage("WeatherInfo of point \"" .
        $pointB . "\" at date " . $dateB->format("Y-m-d H:i:s") . " not found");

        $info = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}");
        $repository->save($info);

        $returned = $repository->findCloseByDateAndPoint($pointB, $dateB);
    }
}
