<?php

namespace Weather\Domain\Model\Weather;

use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfo;

interface WeatherInfoRepositoryInterface
{
    public function add(WeatherInfo $info): void;

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findById(WeatherInfoId $id): WeatherInfo;
}
