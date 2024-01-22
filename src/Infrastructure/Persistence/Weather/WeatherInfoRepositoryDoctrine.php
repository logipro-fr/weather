<?php

namespace Weather\Infrastructure\Persistence\Weather;

use Doctrine\ORM\EntityRepository;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

/**
 * @extends EntityRepository<WeatherInfo>
 */
class WeatherInfoRepositoryDoctrine extends EntityRepository implements WeatherInfoRepositoryInterface
{
    public function save(WeatherInfo $info): void
    {
        $manager = $this->getEntityManager();
        $manager->persist($info);
    }

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findById(WeatherInfoId $id): WeatherInfo
    {
        $info = $this->getEntityManager()->find(WeatherInfo::class, $id);
        if($info != null){
            return $info;
        }
        throw new WeatherInfoNotFoundException("Object WeatherInfo of ID \"" . $id . "\" not found");
    }

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        return new WeatherInfo(new Point(0, 0), new DateTimeImmutable("1970-01-01"), "{}");
    }

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findCloseByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        return new WeatherInfo(new Point(0, 0), new DateTimeImmutable("1970-01-01"), "{}");
    }
}
