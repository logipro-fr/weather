<?php

namespace Weather\Domain\Model\Weather;

class Point
{
    //private const PATTERN_STRING = "[%g,%g]";

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
}
