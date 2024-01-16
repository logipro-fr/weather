<?php

namespace Weather\WeatherStack\Application\Service\GetCurrentWeather;

use Weather\Application\Share\Response;

class GetCurrentWeatherResponse implements Response
{
    /**
     *
     * @param array<string,string> $weatherHotPoints
     * @return void
     */
    public function __construct(
        public readonly \stdClass $report,
        public readonly array $weatherHotPoints,
    ) {
    }
}
