<?php

namespace Weather\Share\Domain;

use Safe\DateTimeImmutable;

use function SafePHP\intval;

class LocationTime
{
    private const MAX_DISTANCE_BETWEEN_SIMILAR = 1000;
    private const MAX_DELAY_BETWEEN_SIMILAR = 30;
    private const ZERO_MICROSECOND = 0;
    private const SECONDS_IN_MINUTE = 60;

    public function __construct(
        private float $latitude,
        private float $longitude,
        private DateTimeImmutable|\DateTimeImmutable $date,
    ) {
        $this->date = $this->setPrecisenessToSeconds($date);
    }

    private function setPrecisenessToSeconds(
        DateTimeImmutable|\DateTimeImmutable $date
    ): DateTimeImmutable|\DateTimeImmutable {
        list($hour,$minute,$second) = explode(":", $date->format("H:i:s"));

        return $date->setTime(
            intval($hour),
            intval($minute),
            intval($second),
            self::ZERO_MICROSECOND
        );
    }

    public function __toString(): string
    {
        return sprintf("%s %s %s", $this->latitude, $this->longitude, $this->date->format("Y-m-d H:i:s"));
    }

    public function getPoint(): Point
    {
        return new Point($this->latitude, $this->longitude);
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getTime(): DateTimeImmutable
    {
        if ($this->date instanceof DateTimeImmutable) {
            return $this->date;
        }
        return DateTimeImmutable::createFromRegular($this->date);
    }

    public function equals(LocationTime $other): bool
    {
        return $this->latitude === $other->latitude
            && $this->longitude === $other->longitude
            && $this->date->format("Y-m-d H:i:s") === $other->date->format("Y-m-d H:i:s");
    }

    public function isSimilarTo(
        LocationTime $other,
        int $maxDistance = self::MAX_DISTANCE_BETWEEN_SIMILAR,
        int $maxDelay = self::MAX_DELAY_BETWEEN_SIMILAR
    ): bool {
        if (abs($this->minutesBetween($this->getTime(), $other->getTime())) > $maxDelay) {
            return false;
        }
        if ($this->getPoint()->distance($other->getPoint()) > $maxDistance) {
            return false;
        }
        return true;
    }

    private function minutesBetween(DateTimeImmutable $date1, DateTimeImmutable $date2): float
    {
        return -($date1->getTimestamp() - $date2->getTimestamp()) / self::SECONDS_IN_MINUTE;
    }

    public function isSimilarOnNextHourTo(
        LocationTime $other,
        int $maxDistance = self::MAX_DISTANCE_BETWEEN_SIMILAR
    ): bool {
        $minutesInterval = $this->minutesBetween($this->getTime(), $other->getTime());
        if (abs($minutesInterval) > self::SECONDS_IN_MINUTE || $minutesInterval < 0) {
            return false;
        }
        if ($this->getPoint()->distance($other->getPoint()) > $maxDistance) {
            return false;
        }
        return true;
    }
}
