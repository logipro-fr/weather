<?php

namespace Weather\WeatherStack\Application\Service;

use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;

class CleanHistoricalDay
{
    public function __construct(private HistoricalDayRepositoryInterface $repository)
    {
    }

    public function execute(): void
    {
        $this->repository->clean();
    }
}
