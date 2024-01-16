<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\CurrentWeather;

use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherRepositoryInterface;
use Weather\WeatherStack\Domain\Model\CurrentWeather\Exceptions\CurrentWeatherNotFoundException;
use Safe\DateTimeImmutable;

class CurrentWeatherRepositoryInMemory implements CurrentWeatherRepositoryInterface
{
    /** @var array<CurrentWeather> */
    private array $currents = [];

    public function add(CurrentWeather $currentWeather): void
    {
        $this->currents[] = $currentWeather;
    }


    public function findById(CurrentWeatherId $id): CurrentWeather
    {
        foreach ($this->currents as $currentWeather) {
            if ($currentWeather->getId()->equals($id)) {
                return $currentWeather;
            }
        }

        throw new CurrentWeatherNotFoundException();
    }

    /**
     * @return array<CurrentWeather>
     */
    public function findRequestedAt(DateTimeImmutable $firstDate, DateTimeImmutable $lastDate): array
    {
        $results = [];

        foreach ($this->currents as $currentWeather) {
            $weatherTime = $currentWeather->getRequestAt()->getTime()->getTimestamp();
            if ($weatherTime >= $firstDate->getTimestamp() && $weatherTime <= $lastDate->getTimestamp()) {
                $results[] = $currentWeather;
            }
        }

        usort($this->currents, function (CurrentWeather $a, CurrentWeather $b) {
            $timeA = $a->getRequestAt()->getTime();
            $timeB = $b->getRequestAt()->getTime();

            return ($timeA < $timeB) ? -1 : 1;
        });

        return $results;
    }
}
