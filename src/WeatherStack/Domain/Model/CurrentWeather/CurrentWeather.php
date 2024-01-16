<?php

namespace Weather\WeatherStack\Domain\Model\CurrentWeather;

use Weather\Share\Domain\LocationTime;

use function Safe\json_decode;
use function SafePHP\floatval;

class CurrentWeather
{
    public function __construct(
        private CurrentWeatherId $currentWeatherId,
        private LocationTime $requestedAt,
        private string $jsonCurrentWeather,
    ) {
    }

    public function getId(): CurrentWeatherId
    {
        return $this->currentWeatherId;
    }

    public function getRequestAt(): LocationTime
    {
        return $this->requestedAt;
    }

    /**
     * @return string|float|array<string>
     */
    public function getCurrent(string $fieldName): string|float|array
    {
        $fullResponseRequest = (object)json_decode($this->jsonCurrentWeather);
        $currentWeather = $fullResponseRequest->current;

        $value = match ($fieldName) {
            'temperature','weather_code','wind_speed',
            'wind_degree','pressure','precip','humidity','cloudcover','feelslike','uv_index','visibility'
             => floatval($currentWeather->$fieldName),
            'observation_time','wind_dir' => strval($currentWeather->$fieldName),
            'weather_icons','weather_descriptions' => (array)$currentWeather->$fieldName,
            default => ""
        };

        return $value;
    }

    public function getJsonCurrentWeather(): string
    {
        return $this->jsonCurrentWeather;
    }
}
