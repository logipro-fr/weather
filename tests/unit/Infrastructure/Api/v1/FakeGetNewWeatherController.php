<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use Weather\APIs\WeatherApiInterface;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Symfony\GetNewWeatherController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class FakeGetNewWeatherController extends GetNewWeatherController
{
    public function __construct(
        WeatherApiInterface $api,
        WeatherInfoRepositoryInterface $repository = new WeatherInfoRepositoryInMemory()
    ) {
        $this->repository = $repository;
        $this->api = $api;
    }
}
