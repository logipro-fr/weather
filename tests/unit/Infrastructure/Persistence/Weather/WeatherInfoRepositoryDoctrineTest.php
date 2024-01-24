<?php

namespace Weather\Tests\Infrastructure\Persistence\Weather;

use DoctrineTestingTools\DoctrineRepositoryTesterTrait;

class WeatherInfoRepositoryDoctrineTest extends WeatherInfoRepositoryInMemoryTest
{
    use DoctrineRepositoryTesterTrait;

    protected function setUp(): void
    {
        $this->initDoctrineTester();
        $this->clearTables(["weatherinfos"]);
        $this->repository = new WeatherInfoRepositoryDoctrineFake($this->getEntityManager());
    }
}
