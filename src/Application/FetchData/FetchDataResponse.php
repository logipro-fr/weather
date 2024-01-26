<?php

namespace Weather\Application\FetchData;

use Weather\Application\Presenter\ResponseInterface;
use Weather\Domain\Model\Weather\WeatherInfo;

class FetchDataResponse implements ResponseInterface
{
    public function __construct(private readonly WeatherInfo $info)
    {
    }

    public function getData(): WeatherInfo
    {
        return $this->info;
    }
}
