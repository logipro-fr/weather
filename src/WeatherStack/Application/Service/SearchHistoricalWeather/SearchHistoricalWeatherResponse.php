<?php

namespace Weather\WeatherStack\Application\Service\SearchHistoricalWeather;

use Weather\Application\Share\Response;
use Weather\WeatherStack\Domain\Model\HistoricalHour;

class SearchHistoricalWeatherResponse implements Response
{
    /**
     * @param array<int,array<HistoricalHour>> $wishedHourArrays
     * @param array<LocationTimeDTO> $failures
     */
    public function __construct(
        public readonly array $wishedHourArrays,
        public readonly int $apiQueryCount,
        public readonly int $storedQueryCount,
        public readonly array $failures,
    ) {
    }
}
