<?php

namespace Weather\Application\FetchData;

use Weather\Application\Presenter\AbstractResponse;
use Weather\Domain\Model\Weather\WeatherInfo;

class FetchDataResponse extends AbstractResponse
{
    public function __construct(private readonly WeatherInfo $info)
    {
    }

    public function getData(): WeatherInfo
    {
        return $this->info;
    }
}
