<?php

namespace Weather\WeatherStack\Domain\Model;

use Weather\Share\Domain\LocationTime;
use Safe\DateTimeImmutable;

class HistoricalHourId
{
    private const DATETIME_FORMAT = 'Y-m-d H';

    public function __construct(
        private LocationTime $locationTime,
    ) {
        $this->locationTime = new LocationTime(
            $locationTime->getPoint()->getLatitude(),
            $locationTime->getPoint()->getLongitude(),
            $locationTime->getTime()->createFromFormat("Y-m-d H:i", $locationTime->getTime()->format("Y-m-d H:00"))
        );
    }

    public function equals(HistoricalHourId $id): bool
    {
        return $this->locationTime->getTime()->format(
            self::DATETIME_FORMAT
        ) === $id->locationTime->getTime()->format(self::DATETIME_FORMAT) &&
            $this->locationTime->getLatitude() == $id->getLocationTime()->getLatitude() &&
            $this->locationTime->getLongitude() == $id->getLocationTime()->getLongitude();
    }

    public function __toString()
    {
        return $this->locationTime->getTime()->format(self::DATETIME_FORMAT) . " " .
            strval($this->locationTime->getLatitude()) . " " .
            strval($this->locationTime->getLongitude());
    }

    public function getHistoricalDate(): DateTimeImmutable
    {
        return $this->locationTime->getTime();
    }

    public function getLocationTime(): LocationTime
    {
        return $this->locationTime;
    }
}
