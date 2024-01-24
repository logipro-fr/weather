<?php

namespace Weather\Infrastructure\Persistence\Weather;

use DateInterval;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
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
    private const TIME_LOOKUP_RANGE_STRING = "PT1800S";
    private static DateInterval $TIME_LOOKUP_RANGE;

    public function __construct(EntityManagerInterface $manager)
    {
        /** @var ClassMetadata<WeatherInfo> */
        $class = $manager->getClassMetadata(WeatherInfo::class);
        parent::__construct($manager, $class);
        self::$TIME_LOOKUP_RANGE = new DateInterval(self::TIME_LOOKUP_RANGE_STRING);
    }

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
        if ($info != null) {
            return $info;
        }
        throw new WeatherInfoNotFoundException("Object WeatherInfo of ID \"" . $id . "\" not found");
    }

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        /** @var EntityManager $manager */
        $manager = $this->getEntityManager();
        $qb = $manager->createQueryBuilder();

        $conditionA = $qb->expr()
            ->eq("w.point", "'" . $point->__toString() . "'");
        $conditionB = $qb->expr()
            ->eq("w.date", $date->format("'Y-m-d H:i:s.u'"));

        $info = $qb->select("w")
            ->from(WeatherInfo::class, "w")->where($conditionA, $conditionB);

        /** @var array<WeatherInfo> $result */
        $result = $info->getQuery()->getResult();
        if ($result == null) {
            throw new WeatherInfoNotFoundException("WeatherInfo of point \"" .
                $point . "\" at date " . $date->format("Y-m-d H:i:s.u") . " not found");
        }
        return $result[0];
    }

    /**
     * @throws WeatherInfoNotFoundException
     */
    public function findCloseByDateAndPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        /** @var EntityManager $manager */
        $manager = $this->getEntityManager();
        $qb = $manager->createQueryBuilder();

        $before = $date->sub(self::$TIME_LOOKUP_RANGE);
        $after = $date->add(self::$TIME_LOOKUP_RANGE);

        $condition = $qb->expr()
            ->between(
                "w.date",
                $before->format("'Y-m-d H:i:s'"),
                $after->format("'Y-m-d H:i:s'")
            );

        $query = $qb->select("w")
            ->from(WeatherInfo::class, "w")->where($condition);

        /** @var array<WeatherInfo> $result */
        $result = $query->getQuery()->getResult();

        foreach ($result as $info) {
            if ($info->closeTo($point, $date)) {
                return $info;
            }
        }
        throw new WeatherInfoNotFoundException("WeatherInfo of point \"" .
            $point . "\" at date " . $date->format("Y-m-d H:i:s.u") . " not found");
    }
}
