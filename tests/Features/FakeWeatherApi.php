<?php

namespace Weather\Tests\Features;

use Safe\DateTimeImmutable;
use Weather\APIs\WeatherApiInterface;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;

use function Safe\json_encode;

class FakeWeatherApi implements WeatherApiInterface
{
    private const POSSIBLE_WEATHER_TEMPERATURE = [-2,5,10,20,12];
    private const POSSIBLE_WEATHER_SKY = ["Clear", "Cloudy", "Rainfall", "Thunderstorm"];
    private const POSSIBLE_WEATHER_HUMIITY = [12,25,1,8,7,4];
    private const NAME = "fake API for tests";

    /**
     * @var array<WeatherInfo> $lastMultipleReturn
     */
    private array $lastMultipleReturn;
    private WeatherInfo $lastReturn;

    private function getFromSingularPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        $data = ["Forecast" =>
            self::POSSIBLE_WEATHER_SKY[rand(0, sizeof(FakeWeatherApi::POSSIBLE_WEATHER_SKY) - 1)],
        "Temperature" =>
        self::POSSIBLE_WEATHER_TEMPERATURE[
            rand(0, sizeof(self::POSSIBLE_WEATHER_TEMPERATURE) - 1)],
        "Humidity" =>
        self::POSSIBLE_WEATHER_HUMIITY[
            rand(0, sizeof(self::POSSIBLE_WEATHER_HUMIITY) - 1)]
        ];

        $jsonData = json_encode($data);

        $res = new WeatherInfo($point, $date, $jsonData, false, new WeatherInfoId());

        $this->lastReturn = $res;
        return $res;
    }

    /**
     * @param array<Point> $points
     * @return array<WeatherInfo>
     */
    public function getFromPoints(array $points, DateTimeImmutable $date): array
    {
        $res = [];
        foreach ($points as $p) {
            array_push($res, $this->getFromSingularPoint($p, $date));
        }
        $this->lastMultipleReturn = $res;
        return $res;
    }

    /**
     * @return array<WeatherInfo>
     */
    public function getLastReturnFromMultiplePoints(): array
    {
        return $this->lastMultipleReturn;
    }

    public function getLastReturnFromPoint(): WeatherInfo
    {
        return $this->lastReturn;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
