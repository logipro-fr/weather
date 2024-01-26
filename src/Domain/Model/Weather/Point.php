<?php

namespace Weather\Domain\Model\Weather;

class Point
{
    private const DELIMITER = ",";

    public function __construct(
        private readonly float $latitude,
        private readonly float $longitude
    ) {
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function equals(Point $other): bool
    {
        return $this->getLatitude() == $other->getLatitude() &&
            $this->getLongitude() == $other->getLongitude();
    }

    public function __toString()
    {
        return floatval($this->getLatitude()) . Point::DELIMITER . floatval($this->getLongitude());
    }
}
