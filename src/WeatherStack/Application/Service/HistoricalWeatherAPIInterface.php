<?php

namespace Weather\WeatherStack\Application\Service;

use Weather\Share\Domain\LocationTime;

interface HistoricalWeatherAPIInterface
{
    public function getHistoricalWeather(LocationTime $locationTime): HistoricalWeatherInterface;
}
