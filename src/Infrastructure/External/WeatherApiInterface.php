<?php

namespace Weather\Infrastructure\External;

use Safe\DateTimeImmutable;
use Symfony\Contracts\HttpClient\HttpClientInterface;
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

    public static function create(
        ?string $weatherStackApiKey = null,
        HttpClientInterface $httpClient = null
    ): WeatherApiInterface;
}
