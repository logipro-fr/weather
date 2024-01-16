<?php

namespace Weather\WeatherStack\Application\Service;

use Weather\Share\Domain\Point;

interface WeatherAPIInterface
{
    /**
     * @param array<Point> $hotpoints
     * @return array<string,string> <gps format: "lat,lon",json meteo data>
     */
    public function getJsonCurrentWeather(array $hotpoints): array;

    public function getLastRequestNumber(): int;
}
