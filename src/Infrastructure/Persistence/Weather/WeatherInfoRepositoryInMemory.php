<?php

namespace Weather\Infrastructure\Persistence\Weather;

use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class WeatherInfoRepositoryInMemory implements WeatherInfoRepositoryInterface
{
    /**
     * @var array<WeatherInfo> $repository
     */
    private array $repository = [];

    public function save(WeatherInfo $info): void
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

    public function findCloseByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        foreach ($this->repository as $info) {
            if ($info->closeTo($point, $date)) {
                return $info;
            }
        }
        throw new WeatherInfoNotFoundException("WeatherInfo of point \"" .
            $point . "\" at date " . $date->format("Y-m-d H:i:s.u") . " not found");
    }

    public function findByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        foreach ($this->repository as $info) {
            if (
                $info->getPoint()->equals($point) &&
                $info->getdate()->getTimestamp() == $date->getTimestamp()
            ) {
                return $info;
            }
        }
        throw new WeatherInfoNotFoundException("WeatherInfo of point \"" .
            $point . "\" at date " . $date->format("Y-m-d H:i:s.u") . " not found");
    }
}
