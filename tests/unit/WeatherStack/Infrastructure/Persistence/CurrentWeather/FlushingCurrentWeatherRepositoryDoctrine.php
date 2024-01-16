<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\CurrentWeather;

use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Infrastructure\Persistence\CurrentWeather\CurrentWeatherRepositoryDoctrine;

class FlushingCurrentWeatherRepositoryDoctrine extends CurrentWeatherRepositoryDoctrine
{
    public function add(CurrentWeather $currentWeather): void
    {
        parent::add($currentWeather);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->detach($currentWeather);
    }
}
