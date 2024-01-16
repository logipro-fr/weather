<?php

namespace Weather\WeatherStack\Application\Service\RequestCurrentWeather;

use Weather\Share\Domain\Point;

class RequestCurrentWeatherRequest
{
    /**
     * @param array<Point> $hotpoints
     * @return void
     */
    public function __construct(
        public readonly array $hotpoints
    ) {
    }
}
