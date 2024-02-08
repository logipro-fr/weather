<?php

namespace Weather\Tests\Application\FetchData;

use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Application\FetchData\ById\FetchDataById;
use Weather\Application\FetchData\ById\FetchDataByIdRequest;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class FetchDataByIdTest extends TestCase
{
    private WeatherInfo $info;
    private WeatherInfoRepositoryInterface $repository;

    public function setUp(): void
    {
        $this->repository = new WeatherInfoRepositoryInMemory();
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d",
            "2024-01-01",
            new DateTimeZone(date_default_timezone_get())
        );
        $this->info = new WeatherInfo(new Point(41.867, 2.333), $date, "{\"weather\":\"good\"}", Source::DEBUG);
        $this->repository->save($this->info);
    }

    public function testExecute(): void
    {
        $presenter = new PresenterObject();
        $service = new FetchDataById($presenter, $this->repository);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d i",
            "2024-01-01 10",
            new DateTimeZone(date_default_timezone_get())
        );
        $request = new FetchDataByIdRequest($this->info->getId()->__toString());
        $service->execute($request);

        $this->assertEquals($this->info, $presenter->read()->getData());
    }
}
