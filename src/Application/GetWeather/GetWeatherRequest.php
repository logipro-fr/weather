<?php

namespace Weather\Application\GetWeather;

use Weather\Domain\Model\Weather\Point;
use Safe\DateTimeImmutable;

class GetWeatherRequest
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
