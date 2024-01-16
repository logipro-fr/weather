<?php

namespace Weather\WeatherStack\Domain\Model;

use Weather\WeatherStack\Domain\Model\Exceptions\HistoricalDayNotFoundException;

interface HistoricalDayRepositoryInterface
{
    public function add(HistoricalDay $weatherHistoricalFeatures): void;

    public function existById(HistoricalDayId $id): bool;

    /**
     * @throws HistoricalDayNotFoundException
     */
    public function findById(HistoricalDayId $id): HistoricalDay;

    /**
     * @throws HistoricalDayNotFoundException
     */
    public function findByHistoricalHourId(HistoricalHourId $hourId): HistoricalDay;

    public function existdByHistoricalHourId(HistoricalHourId $hourId): bool;

    public function clean(): void;
}
