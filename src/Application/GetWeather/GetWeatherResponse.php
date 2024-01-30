<?php

namespace Weather\Application\GetWeather;

use Weather\Application\Presenter\AbstractResponse;
use Weather\Domain\Model\Weather\WeatherInfo;

class GetWeatherResponse extends AbstractResponse
{
    /** @param array<WeatherInfo> $weatherInfos */
    public function __construct(private readonly array $weatherInfos)
    {
    }

    /** @return array<WeatherInfo> */
    public function getData(): array
    {
        return $this->weatherInfos;
    }

    /** @return array<WeatherInfo> */
    public function jsonSerialize(): array
    {
        return $this->weatherInfos;
    }
}
