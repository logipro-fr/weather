<?php

namespace Weather\Tests\Infrastructure\Persistence\Weather;

use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryDoctrine;

class WeatherInfoRepositoryDoctrineFake extends WeatherInfoRepositoryDoctrine
{
    public function save(WeatherInfo $info): void
    {
        parent::save($info);
        $this->getEntityManager()->flush();
    }
}
