<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\CurrentWeather;

use Weather\Tests\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherBuilder;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherRepositoryInterface;
use Weather\WeatherStack\Domain\Model\CurrentWeather\Exceptions\CurrentWeatherNotFoundException;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

abstract class CurrentWeatherRepositoryTestBase extends TestCase
{
    protected CurrentWeatherRepositoryInterface $repository;

    public function setUp(): void
    {
        $this->createRepository();
    }

    abstract protected function createRepository(): void;

    public function testFindById(): void
    {
        $currentWeather = CurrentWeatherBuilder::aCurrentWeather()->build();
        $id = $currentWeather->getId();

        $this->repository->add($currentWeather);

        $result = $this->repository->findById($id);
        $this->assertEquals($currentWeather, $result);
    }

    public function testNotFoundException(): void
    {
        $this->expectException(CurrentWeatherNotFoundException::class);

        $badId = new CurrentWeatherId("badId");
        $this->repository->findById($badId);
    }

    public function testFindRequestedAt(): void
    {
        $time = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2023-06-21 16:14:25");
        $currentWeather = CurrentWeatherBuilder::aCurrentWeather()
            ->withRequestedAt($time)
            ->build();
        $id = $currentWeather->getId();

        $this->repository->add($currentWeather);

        $result = $this->repository->findRequestedAt($time, $time);
        $this->assertEquals(1, count($result));
        $this->assertEquals($id, $result[0]->getId());
        $this->assertEquals("2023-06-21 16:14:25", $result[0]->getRequestAt()->getTime()->format("Y-m-d H:i:s"));
    }

    public function testFindRequestedAtSeveralCase(): void
    {
        $now = new DateTimeImmutable();
        $currentWeather = CurrentWeatherBuilder::aCurrentWeather()
            ->withRequestedAt($now)
            ->build();
        $this->repository->add($currentWeather);
        $idNow = $currentWeather->getId();

        $less1Minute = $now->modify("-1 minute");
        $currentWeather = CurrentWeatherBuilder::aCurrentWeather()
            ->withRequestedAt($less1Minute)
            ->build();
        $this->repository->add($currentWeather);
        $idLess1Minute = $currentWeather->getId();

        $more1Minute = $now->modify("+1 minute");
        $currentWeather = CurrentWeatherBuilder::aCurrentWeather()
            ->withRequestedAt($more1Minute)
            ->build();
        $this->repository->add($currentWeather);
        $idMore1Minute = $currentWeather->getId();

        $result = $this->repository->findRequestedAt($less1Minute, $more1Minute);
        $this->assertEquals(3, count($result));

        $result = $this->repository->findRequestedAt($more1Minute, $less1Minute);
        $this->assertEquals(0, count($result));

        $result = $this->repository->findRequestedAt($less1Minute, $now);
        $this->assertEquals(2, count($result));
        $this->assertEquals($idLess1Minute, $result[0]->getId());
        $this->assertEquals($idNow, $result[1]->getId());

        $result = $this->repository->findRequestedAt($now, $more1Minute);
        $this->assertEquals(2, count($result));
        $this->assertEquals($idNow, $result[0]->getId());
        $this->assertEquals($idMore1Minute, $result[1]->getId());

        $result = $this->repository->findRequestedAt($less1Minute->modify('+1 sec'), $more1Minute);
        $this->assertEquals(2, count($result));
        $this->assertEquals($idNow, $result[0]->getId());
        $this->assertEquals($idMore1Minute, $result[1]->getId());

        $result = $this->repository->findRequestedAt($now->modify('+1 sec'), $more1Minute);
        $this->assertEquals(1, count($result));
        $this->assertEquals($idMore1Minute, $result[0]->getId());
    }
}
