<?php

namespace Weather\Domain\Model\Weather;

use function SafePHP\strval;

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

    public function __toString()
    {
        return strval($this->getLatitude()) . Point::DELIMITER . strval($this->getLongitude());
    }
}
