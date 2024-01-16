<?php

namespace Weather\WeatherStack\Application\Service\GetCurrentWeather;

use Weather\Share\Domain\Point;

class GetCurrentWeatherRequest
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
