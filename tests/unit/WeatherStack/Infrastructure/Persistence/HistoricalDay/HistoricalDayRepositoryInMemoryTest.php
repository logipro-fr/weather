<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;

class HistoricalDayRepositoryInMemoryTest extends HistoricalDayRepositoryInMemoryTestBase
{
    protected function setUp(): void
    {
        $this->repository = new HistoricalDayRepositoryInMemory();
    }
}
