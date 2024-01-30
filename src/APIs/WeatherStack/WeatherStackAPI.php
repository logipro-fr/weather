<?php

namespace Weather\APIs\WeatherStack;

use Safe\DateTimeImmutable;
use Weather\APIs\WeatherApiInterface;
use Weather\Domain\Model\Weather\WeatherInfo;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherStackAPI implements WeatherApiInterface{

    /**
     * @param array<Point> $points
     * @return array<WeatherInfo>
     */
    public function getFromPoints(array $points, DateTimeImmutable $date): array {
        $historicalDate = $date->format("Y-m-d");
        $query = implode(";", $points);
        $key = "";

        $url = "https://api.weatherstack.com/historical?access_key=" . $key . "&query=$query&units=m&historical_date=$historicalDate&hourly=1&interval=1";

        $response = HttpClient::create()->request("GET", $url, []);
        print($response->getContent());
        return [];
    }
}