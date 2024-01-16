<?php

namespace Weather\WeatherStack\Application\Service\RequestCurrentWeather;

use Weather\Application\Share\Response;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;

class RequestCurrentWeatherResponse implements Response
{
    /**
     *
     * @param array<CurrentWeather> $weatherHotPoints
     * @return void
     */
    public function __construct(
        public readonly Report $report,
        public readonly array $weatherHotPoints,
    ) {
    }
}
