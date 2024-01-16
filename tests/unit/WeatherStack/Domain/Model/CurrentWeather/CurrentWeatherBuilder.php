<?php

namespace Weather\Tests\WeatherStack\Domain\Model\CurrentWeather;

use Weather\Share\Domain\Point;
use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Safe\DateTimeImmutable;

use function Safe\file_get_contents;

class CurrentWeatherBuilder
{
    private function __construct(
        private Point $requestedLocation = new Point(48.863, 2.313),
        private DateTimeImmutable $requestedDate = new DateTimeImmutable(),
        private string $jsonCurrentWeather = "",
    ) {
        $this->requestedDate = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2023-06-21 16:14:25");

        $this->jsonCurrentWeather = file_get_contents(__DIR__ . '/resources/48.863,2.313.json');
    }

    public static function aCurrentWeather(): CurrentWeatherBuilder
    {
        return new CurrentWeatherBuilder();
    }

    public function withBrutContent(string $brutContent): CurrentWeatherBuilder
    {
        $this->jsonCurrentWeather = $brutContent;
        return $this;
    }

    public function withRequestedAt(DateTimeImmutable $requestedAt): CurrentWeatherBuilder
    {
        $this->requestedDate = $requestedAt;
        return $this;
    }

    public function withRequestedLocation(Point $location): CurrentWeatherBuilder
    {
        $this->requestedLocation = $location;
        return $this;
    }

    public function build(): CurrentWeather
    {
        $id = new CurrentWeatherId();
        $requestAt = new LocationTime(
            $this->requestedLocation->getLatitude(),
            $this->requestedLocation->getLongitude(),
            $this->requestedDate
        );
        return new CurrentWeather($id, $requestAt, $this->jsonCurrentWeather);
    }
}
