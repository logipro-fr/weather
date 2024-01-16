<?php

namespace Weather\WeatherStack\Application\Service;

use Weather\Share\Domain\Point;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;

interface CurrentWeatherApiInterface
{
    /**
     * @param array<Point> $hotpoints
     * @return array<CurrentWeather> <gps format: "lat,lon",json meteo data>
     */
    public function getCurrentWeathers(array $hotpoints): array;

    public function getRealisedRequest(): int;
}
