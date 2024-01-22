<?php

namespace Weather\Tests\Infrastructure\Persistence\Weather;

use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryDoctrine;

class WeatherInfoRepositoryDoctrineTest extends WeatherInfoRepositoryInMemoryTest
{
    protected function setUp(): void
    {
        $this->repository = new WeatherInfoRepositoryDoctrine();
    }
}
