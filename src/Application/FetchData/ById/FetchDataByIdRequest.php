<?php

namespace Weather\Application\FetchData\ById;

use Weather\Application\Presenter\RequestInterface;
use Weather\Domain\Model\Weather\WeatherInfoId;

class FetchDataByIdRequest implements RequestInterface
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
