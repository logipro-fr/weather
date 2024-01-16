<?php

namespace Weather\WeatherStack\Domain\Model\CurrentWeather;

use Weather\WeatherStack\Domain\Model\CurrentWeather\Exceptions\CurrentWeatherNotFoundException;
use Safe\DateTimeImmutable;

interface CurrentWeatherRepositoryInterface
{
    public function add(CurrentWeather $currentWeather): void;

    /**
     * @throws CurrentWeatherNotFoundException
     */
    public function findById(CurrentWeatherId $currentWeatherId): CurrentWeather;

    /**
     * @return array<CurrentWeather>
     */
    public function findRequestedAt(DateTimeImmutable $firstDate, DateTimeImmutable $lastDate): array;
}
