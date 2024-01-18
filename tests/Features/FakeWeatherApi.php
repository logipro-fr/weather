<?php

namespace Weather\Tests\Features;

use Safe\DateTimeImmutable;
use Weather\APIs\WeatherApiInterface;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;

class FakeWeatherApi implements WeatherApiInterface
{
    private const POSSIBLE_WEATHER_TEMPERATURE = [-2,5,10,20,12];
    private const POSSIBLE_WEATHER_SKY = ["Clear", "Cloudy", "Rainfall", "Thunderstorm"];
    private const POSSIBLE_WEATHER_HUMIITY = [12,25,1,8,7,4];

    /**
     * @var array<WeatherInfo> $lastMultipleReturn
     */
    private array $lastMultipleReturn;
    private WeatherInfo $lastReturn;

    private function getFromSingularPoint(Point $point, DateTimeImmutable $date): WeatherInfo
    {
        $jsonData = "{\"Forecast\":\"" .
            FakeWeatherApi::POSSIBLE_WEATHER_SKY[rand(0, sizeof(FakeWeatherApi::POSSIBLE_WEATHER_SKY) - 1)] .
        "\",\"Temperature\":" .
        FakeWeatherApi::POSSIBLE_WEATHER_TEMPERATURE[
            rand(0, sizeof(FakeWeatherApi::POSSIBLE_WEATHER_TEMPERATURE) - 1)] .
        "\",\"Temperature\":" .
        FakeWeatherApi::POSSIBLE_WEATHER_HUMIITY[
            rand(0, sizeof(FakeWeatherApi::POSSIBLE_WEATHER_HUMIITY) - 1)] . "%\"}";

        $res = new WeatherInfo($point, $date, $jsonData);
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
}
