<?php

namespace Weather\Application\GetWeather;

use Weather\Domain\Model\Weather\Point;
use Safe\DateTimeImmutable;
use Weather\Application\Presenter\RequestInterface;

class GetWeatherRequest implements RequestInterface
{
    /**
     * @param array<Point> $requestedPoints
     */
    public function __construct(
        private readonly array $requestedPoints,
        private readonly DateTimeImmutable $requestedDate
    ) {
    }

    /**
     * @return array<Point>
     */
    public function getRequestedPoints(): array
    {
        return $this->requestedPoints;
    }

    public function getRequestedDate(): DateTimeImmutable
    {
        return $this->requestedDate;
    }
}
