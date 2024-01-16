<?php

namespace Weather\WeatherStack\Domain\Model;

use Weather\Share\Domain\Point;
use Safe\DateTimeImmutable;

class HistoricalDayId
{
    private const DATE_FORMAT = 'Y-m-d';

    public function __construct(
        private Point $location,
        private DateTimeImmutable $historical
    ) {
        $this->historical = $this->historical->createFromFormat("Y-m-d H:i", $this->historical->format("Y-m-d 00:00"));
    }
    public function equals(HistoricalDayId $id): bool
    {
        return $this->historical->format(self::DATE_FORMAT) === $id->historical->format(self::DATE_FORMAT) &&
            $this->location->getLatitude() == $id->location->getLatitude() &&
            $this->location->getLongitude() == $id->location->getLongitude();
    }

    public function __toString()
    {
        return $this->historical->format(self::DATE_FORMAT) . " " .
            strval($this->location->getLatitude()) . " " .
            strval($this->location->getLongitude());
    }

    public function getHistoricalDate(): DateTimeImmutable
    {
        return $this->historical;
    }

    public function getPoint(): Point
    {
        return $this->location;
    }
}
