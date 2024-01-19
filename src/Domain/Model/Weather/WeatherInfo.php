<?php

namespace Weather\Domain\Model\Weather;

use Phariscope\Event\EventPublisher;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Event\WeatherInfoCreated;

class WeatherInfo
{
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

    public function isHistorical(){
        return $this->isHistorical;
    }
}
