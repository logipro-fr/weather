<?php

namespace Weather\WeatherStack\Application\Service\SearchHistoricalWeather;

class LocationTimeDTO
{
    public function __construct(
        public readonly float $latitude,
        public readonly float $longitude,
        public readonly string $time,
    ) {
    }
}
