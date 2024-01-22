<?php

namespace Weather\Domain\Model\Weather;

use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfo;

interface WeatherInfoRepositoryInterface
{
    public function save(WeatherInfo $info): void;

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findById(WeatherInfoId $id): WeatherInfo;

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo;

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findCloseByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
}
