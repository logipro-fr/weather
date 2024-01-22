<?php

namespace Weather\Tests\Infrastructure\Persistence\Weather;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\Point;
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
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");

        $this->repository->save($info);
        $info_returned = $this->repository->findById($info->getId());
        $this->assertEquals($info, $info_returned);
    }

    public function testAddTwo(): void
    {
        $pointa = new Point(0, 0);
        $pointb = new Point(0, 0);
        $datea = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $dateb = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 13:00");
        $infoa = new WeatherInfo($pointa, $datea, "{\"weather\":\"great\"}");
        $infob = new WeatherInfo($pointb, $dateb, "{\"weather\":\"bad\"}");

        $this->repository->save($infoa);
        $this->repository->save($infob);
        $info_returned = $this->repository->findById($infob->getId());
        $this->assertEquals($infob, $info_returned);
    }

    public function testFind(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $this->repository->save($info);

        $returned = $this->repository->findById($info->getId());

        $this->assertEquals($returned->getId(), $info->getId());
    }

    public function testDoesNotFind(): void
    {
        $this->expectException(WeatherInfoNotFoundException::class);
        $id = new WeatherInfoId();
        $this->expectExceptionMessage("Object WeatherInfo of ID \"" . $id . "\" not found");

        $this->repository->findById($id);
    }

    public function testDoesNotFindFilled(): void
    {
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $infoa = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $infob = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $infoc = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $infod = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");
        $this->repository->save($infoa);
        $this->repository->save($infob);
        $this->repository->save($infoc);
        $this->repository->save($infod);

        $this->expectException(WeatherInfoNotFoundException::class);
        $this->repository->findById(new WeatherInfoId());
    }
}
