<?php

namespace Weather\Tests\WeatherStack\Application\Service;

use Weather\Share\Domain\Point;
use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Application\Service\CleanHistoricalDay;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class CleanRepositoryTest extends TestCase
{
    private HistoricalDayRepositoryInMemory $repository;

    private HistoricalDay $badDay;

    protected function setUp(): void
    {
        $this->repository = new HistoricalDayRepositoryInMemory();
        $this->badDay = new HistoricalDay(
            new HistoricalDayId(new Point(1, 2), new DateTimeImmutable()),
            '{"bad":"content"}',
        );
        $this->repository->add($this->badDay);
    }

    public function testExecute(): void
    {
        $badId = clone $this->badDay->getId();
        $this->assertTrue($this->repository->existById($badId));

        $service = new CleanHistoricalDay($this->repository);
        $service->execute();

        $this->assertFalse($this->repository->existById($badId));
    }
}
