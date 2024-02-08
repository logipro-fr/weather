<?php

namespace Weather\Tests\Application\FetchData;

use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPointRequest;
use Weather\Application\FetchData\ByDateAndPoint\FetchDataByDateAndPoint;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class FetchDataByDateAndPointTest extends TestCase
{
    private WeatherInfo $info;
    private WeatherInfoRepositoryInterface $repository;

    public function setUp(): void
    {
        $this->repository = new WeatherInfoRepositoryInMemory();
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d i",
            "2024-01-01 01",
            new DateTimeZone(date_default_timezone_get())
        );
        $this->info = new WeatherInfo(new Point(41.867, 2.333), $date, "{\"weather\":\"good\"}", Source::DEBUG);
        $this->repository->save($this->info);
    }

    public function testExecuteImprecise(): void
    {
        $presenter = new PresenterObject();
        $service = new FetchDataByDateAndPoint($presenter, $this->repository);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d i",
            "2024-01-01 01",
            new DateTimeZone(date_default_timezone_get())
        );

        $request = new FetchDataByDateAndPointRequest(new Point(41.865, 2.335), $date);

        $service->execute($request);

        $this->assertEquals($this->info, $presenter->read()->getData());
    }

    public function testExecuteExact(): void
    {
        $presenter = new PresenterObject();
        $service = new FetchDataByDateAndPoint($presenter, $this->repository);
        $date = DateTimeImmutable::createFromFormat(
            "Y-m-d i",
            "2024-01-01 01",
            new DateTimeZone(date_default_timezone_get())
        );

        $request = new FetchDataByDateAndPointRequest(new Point(41.867, 2.333), $date, null, true);

        $service->execute($request);

        $this->assertEquals($this->info, $presenter->read()->getData());
    }
}
