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

        $repository->add($info);
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

        $repository->add($infoa);
        $repository->add($infob);
        $info_returned = $repository->findById($infob->getId());
        $this->assertEquals($infob, $info_returned);
    }

    public function testFind(): void
    {
        $repository = new WeatherInfoRepositoryInMemory();
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $repository->add($info);

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
        $repository->add($infoa);
        $repository->add($infob);
        $repository->add($infoc);
        $repository->add($infod);

        $this->expectException(WeatherInfoNotFoundException::class);
        $repository->findById(new WeatherInfoId());
    }
}