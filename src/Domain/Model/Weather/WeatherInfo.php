<?php

namespace Weather\Domain\Model\Weather;

use Phariscope\Event\EventPublisher;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Event\WeatherInfoCreated;

class WeatherInfo
{
    private const ACCEPTABLE_LONGITUDE_DIFF = 0.05;
    private const ACCEPTABLE_LATTITUDE_DIFF = 0.05;
    private const ACCEPTABLE_TIME_DIFF = 1800; //unix timestamp is in seconds, 30m * 60s/m = 1800s

    public function __construct(
        private readonly Point $point,
        private readonly DateTimeImmutable $date,
        private readonly string $data,
        private readonly bool $isHistorical = false,
        private readonly WeatherInfoId $identifier = new WeatherInfoId()
    ) {
        EventPublisher::instance()->publish(new WeatherInfoCreated($identifier));
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getId(): WeatherInfoId
    {
        return $this->identifier;
    }

    public function isHistorical(): bool
    {
        return $this->isHistorical;
    }

    /**
     * @param null|bool $isHistorical null if does not matter
     */
    public function closeTo(Point $point, DateTimeImmutable $date, ?bool $isHistorical = null): bool
    {
        if ($isHistorical != null && $isHistorical != $this->isHistorical()) {
            return false;
        }
        if (abs($this->point->getLatitude() - $point->getLatitude()) > WeatherInfo::ACCEPTABLE_LATTITUDE_DIFF) {
            return false;
        }
        if (abs($this->point->getLongitude() - $point->getLongitude()) > WeatherInfo::ACCEPTABLE_LONGITUDE_DIFF) {
            return false;
        }
        return abs($this->date->getTimestamp() - $date->getTimestamp()) <= WeatherInfo::ACCEPTABLE_TIME_DIFF;
    }
}
