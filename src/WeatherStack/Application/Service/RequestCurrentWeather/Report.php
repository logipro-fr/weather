<?php

namespace Weather\WeatherStack\Application\Service\RequestCurrentWeather;

use Safe\DateTimeImmutable;

class Report
{
    public function __construct(
        public readonly int $requestRealized,
        public readonly int $hotpointNumber,
        public readonly DateTimeImmutable $requestedAt,
        public readonly DateTimeImmutable $finishedAt,
    ) {
    }
}
