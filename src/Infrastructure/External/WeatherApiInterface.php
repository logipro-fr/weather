<?php

namespace Weather\Infrastructure\External;

use Safe\DateTimeImmutable;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;

interface WeatherApiInterface
{
    /**
     * @param array<Point> $points
     * @return array<WeatherInfo>
     */
    public function getFromPoints(array $points, DateTimeImmutable $date): array;

    public function getName(): string;
}
