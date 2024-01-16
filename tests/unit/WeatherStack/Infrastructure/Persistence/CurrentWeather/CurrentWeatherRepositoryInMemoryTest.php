<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\CurrentWeather;

use Weather\WeatherStack\Infrastructure\Persistence\CurrentWeather\CurrentWeatherRepositoryInMemory;

class CurrentWeatherRepositoryInMemoryTest extends CurrentWeatherRepositoryTestBase
{
    protected function createRepository(): void
    {
        $this->repository = new CurrentWeatherRepositoryInMemory();
    }
}
