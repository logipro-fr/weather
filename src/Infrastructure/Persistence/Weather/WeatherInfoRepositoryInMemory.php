<?php

namespace Weather\Infrastructure\Persistence\Weather;

use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class WeatherInfoRepositoryInMemory implements WeatherInfoRepositoryInterface
{
    /**
     * @var array<WeatherInfo> $repository
     */
    private array $repository = [];

    public function add(WeatherInfo $info): void
    {
        array_push($this->repository, $info);
    }

    public function findById(WeatherInfoId $id): WeatherInfo
    {
        foreach ($this->repository as $info) {
            if ($info->getId()->equals($id)) {
                return $info;
            }
        }
        throw new WeatherInfoNotFoundException("Object WeatherInfo of ID \"" . $id . "\" not found");
    }
}
