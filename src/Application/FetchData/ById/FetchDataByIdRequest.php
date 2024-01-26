<?php

namespace Weather\Application\FetchData\ById;

use Weather\Domain\Model\Weather\WeatherInfoId;

class FetchDataByIdRequest
{
    public function __construct(
        private readonly string $Id,
    ) {
    }

    public function getRequestedId(): WeatherInfoId
    {
        return new WeatherInfoId($this->Id);
    }
}
