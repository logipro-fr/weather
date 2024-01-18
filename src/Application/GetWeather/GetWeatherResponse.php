<?php

namespace Weather\Application\GetWeather;

use Weather\Application\Presenter\ResponseInterface;

class GetWeatherResponse implements ResponseInterface
{
    public function __construct(private readonly string $jsonWeather)
    {
    }

    public function getData(): string
    {
        return $this->jsonWeather;
    }
}
