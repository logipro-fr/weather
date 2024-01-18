<?php

namespace Weather\Application\GetWeather;

use Weather\Domain\Model\Weather\Point;
use Safe\DateTimeImmutable;

class GetWeatherRequest
{
    /**
     * @param array<Point> $points
     */
    public function __construct(private readonly array $points, private readonly DateTimeImmutable $date)
    {
    }

    /**
     * @return array<Point>
     */
    public function getPoints(): array
    {
        return $this->points;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
