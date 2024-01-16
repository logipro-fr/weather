<?php

namespace Weather\WeatherStack\Application\Service\SearchHistoricalWeather;

class SearchHistoricalWeatherRequest
{
    /**
     * @param array<LocationTimeDTO> $locationTimes
     * @param array<int> $otherHours
     */
    public function __construct(
        public readonly array $locationTimes,
        public readonly array $otherHours
    ) {
    }
}
