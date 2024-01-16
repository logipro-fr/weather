<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\Share\Domain\Point;
use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\Exceptions\HistoricalDayNotFoundException;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use Weather\Tests\WeatherStack\Domain\Model\HistoricalDayBuilder;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class HistoricalDayRepositoryInMemoryTestBase extends TestCase
{
    protected HistoricalDayRepositoryInterface $repository;

    public function testFindById(): void
    {
        $historicalDay = HistoricalDayBuilder::aHistoricalDay()
            ->build();
        $id = $historicalDay->getId();

        $this->repository->add($historicalDay);

        $result = $this->repository->findById($id);
        $this->assertEquals($historicalDay, $result);
    }

    public function testExistById(): void
    {
        $historicalDay = HistoricalDayBuilder::aHistoricalDay()->build();
        $id = $historicalDay->getId();

        $this->repository->add($historicalDay);

        $this->assertTrue($this->repository->existById($id));

        $badId = new HistoricalDayId(new Point(1, 2), new DateTimeImmutable());
        $this->assertFalse($this->repository->existById($badId));
    }

    public function testNotFoundException(): void
    {
        $this->expectException(HistoricalDayNotFoundException::class);

        $badId = new HistoricalDayId(new Point(1, 2), new DateTimeImmutable());
        $this->repository->findById($badId);
    }

    public function testFindByHistoricalHour(): void
    {
        $historicalDay = HistoricalDayBuilder::aHistoricalDay()->build();
        $this->repository->add($historicalDay);

        $historicalHour = $historicalDay->makeHistoricalHour(10);

        $result = $this->repository->findByHistoricalHourId($historicalHour->getId());
        $this->assertEquals($historicalDay, $result);
    }

    public function testExistByHistoricalHourId(): void
    {
        $historicalDay = HistoricalDayBuilder::aHistoricalDay()->build();
        $id = $historicalDay->getId();

        $this->repository->add($historicalDay);

        $historicalHour = $historicalDay->makeHistoricalHour(10);

        $this->assertTrue($this->repository->existdByHistoricalHourId($historicalHour->getId()));

        $badId = new HistoricalHourId(new LocationTime(1, 2, new DateTimeImmutable()));
        $this->assertFalse($this->repository->existdByHistoricalHourId($badId));
    }

    public function testCleanWeatherStackAPIError(): void
    {
        $badDay = new HistoricalDay(
            new HistoricalDayId(new Point(1, 2), new DateTimeImmutable()),
            '{"success":false,"error":{"code":615,"type":"request_failed",' .
            '"info":"Your API request failed. Please try again or contact support."}}',
        );
        $goodDay = HistoricalDayBuilder::aHistoricalDay()->build();
        $this->repository->add($badDay);
        $this->repository->add($goodDay);

        $badId = clone $badDay->getId();
        $this->assertTrue($this->repository->existById($badId));
        $this->repository->clean();
        $this->assertFalse($this->repository->existById($badId));

        $this->assertTrue($this->repository->existById($goodDay->getId()));
    }

    public function testCleanAnotherBadContent(): void
    {
        $goodDay = HistoricalDayBuilder::aHistoricalDay()->build();
        $this->repository->add($goodDay);

        $badDay = new HistoricalDay(
            new HistoricalDayId(new Point(1, 2), new DateTimeImmutable()),
            '{"bad":"content"}',
        );
        $this->repository->add($badDay);

        $goodDay2 = HistoricalDayBuilder::aHistoricalDay()->build();
        $this->repository->add($goodDay2);

        $badId = clone $badDay->getId();
        $this->assertTrue($this->repository->existById($badId));

        $this->repository->clean();

        $this->assertFalse($this->repository->existById($badId));
        $this->assertTrue($this->repository->existById($goodDay->getId()));
    }

    public function testCleanAnotherVeryBadContent(): void
    {
        $goodDay = HistoricalDayBuilder::aHistoricalDay()->build();
        $this->repository->add($goodDay);

        $badDay = new HistoricalDay(
            new HistoricalDayId(new Point(1, 2), new DateTimeImmutable()),
            'very bad content',
        );
        $this->repository->add($badDay);

        $goodDay2 = HistoricalDayBuilder::aHistoricalDay()->build();
        $this->repository->add($goodDay2);

        $badId = clone $badDay->getId();
        $this->assertTrue($this->repository->existById($badId));

        $this->repository->clean();

        $this->assertFalse($this->repository->existById($badId));
        $this->assertTrue($this->repository->existById($goodDay->getId()));
    }
}
