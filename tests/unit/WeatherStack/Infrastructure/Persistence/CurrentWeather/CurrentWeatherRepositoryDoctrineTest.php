<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\CurrentWeather;

use Weather\Tests\Share\Infrastructure\Doctrine\DoctrineRepositoryTesterTrait;

class CurrentWeatherRepositoryDoctrineTest extends CurrentWeatherRepositoryTestBase
{
    use DoctrineRepositoryTesterTrait;

    protected function createRepository(): void
    {
        $this->resetDatabase(["currentweathers"]);

        $this->repository = new FlushingCurrentWeatherRepositoryDoctrine(
            $this->getEntityManager()
        );
    }
}
