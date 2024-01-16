<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\CurrentWeather;

use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherRepositoryInterface;
use Weather\WeatherStack\Domain\Model\CurrentWeather\Exceptions\CurrentWeatherNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Safe\DateTimeImmutable;

/**
 * @extends EntityRepository<CurrentWeather>
 */
class CurrentWeatherRepositoryDoctrine extends EntityRepository implements CurrentWeatherRepositoryInterface
{
    public function __construct(EntityManagerInterface $em)
    {
        /** @var ClassMetadata<CurrentWeather> $classMD */
        $classMD = $em->getClassMetadata(CurrentWeather::class);
        parent::__construct($em, $classMD);
    }

    public function add(CurrentWeather $currentWeather): void
    {
        $this->getEntityManager()->persist($currentWeather);
    }

    public function findById(CurrentWeatherId $id): CurrentWeather
    {
        $cw = $this->getEntityManager()->find(CurrentWeather::class, $id);
        if (null != $cw) {
            return $cw;
        }
        throw new CurrentWeatherNotFoundException();
    }

    /**
     * @return array<CurrentWeather>
     */
    public function findRequestedAt(DateTimeImmutable $firstDate, DateTimeImmutable $lastDate): array
    {
        $patternDate = "Y-m-d H:i:s";
        $qb = $this->createQueryBuilder('w')
            ->where("w.requestedAt.date >= :firstDate")
            ->andWhere("w.requestedAt.date <= :lastDate")
            ->setParameter('firstDate', $firstDate->format($patternDate))
            ->setParameter('lastDate', $lastDate->format($patternDate))
            ->orderBy('w.requestedAt.date', 'ASC');
        $query = $qb->getQuery();

        /** @var array<int,CurrentWeather>  */
        $weathers = $query->getResult();

        return $weathers;
    }
}
