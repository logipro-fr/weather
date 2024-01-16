<?php

namespace Weather\Tests\WeatherStack\TestTools;

use Closure;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class FakeGenerator
{
    /** @var array<string,\stdClass> */
    private array $cached = [];
    /** @var array<string,\stdClass> */
    private array $stations = [];

    public function __construct()
    {
        /** @var array<int,\stdClass> */
        $hotpoints = json_decode((string)file_get_contents(__DIR__ . '/resources/query/bulk-2500-gps.json'));
        foreach ($hotpoints as $wsPoint) {
            $key = $wsPoint->request->gps->on_Latitude . "," . $wsPoint->request->gps->on_Longitude;
            $this->cached[$key] = $wsPoint;

            $keyStation = $wsPoint->location->lat . "," . $wsPoint->location->lon;
            if (!isset($this->stations[$keyStation])) {
                $wsPoint->request->query = sprintf(
                    "Lat %s and Lon %s",
                    round($wsPoint->location->lat, 2),
                    round($wsPoint->location->lon, 2)
                );
                $wsPoint->request->gps->on_Latitude = $wsPoint->location->lat;
                $wsPoint->request->gps->on_Longitude = $wsPoint->location->lon;
                $this->stations[$keyStation] = $wsPoint;
            }
        }
    }

    public function getMockHttpClient(): MockHttpClient
    {
        $callable = Closure::fromCallable([$this,'callable']);

        return new MockHttpClient($callable);
    }

    /**
     *
     * @param array<int,mixed> $options
     * @return MockResponse
     */
    private function callable(string $method, string $url, array $options): MockResponse
    {
        $parsedUrl = strval(parse_url($url, PHP_URL_QUERY));
        parse_str($parsedUrl, $params);
        $queryParam = strval(is_array($params['query']) ? "" : $params['query']);
        if (isset($params['historical_date'])) {
            $filename = $queryParam . '.json';
            $expectedResponse = $this->unsetGPS(
                (string)file_get_contents(__DIR__ . '/resources/historical/' . $filename)
            );
            return new MockResponse($expectedResponse);
        }
        $filename = __DIR__ . '/resources/query/' . $queryParam . '.json';
        if (file_exists($filename)) {
            $expectedResponse = $this->unsetGPS((string)file_get_contents($filename));
            return new MockResponse($expectedResponse);
        }

        $points = explode(";", $queryParam);
        $wsPoints = [];
        foreach ($points as $point) {
            if (isset($this->cached[$point])) {
                $expectedResponse = $this->unsetGPS((string)json_encode($this->cached[$point]));
                $wsPoints[] = json_decode($expectedResponse);
            } else {
                if (isset($this->stations[$point])) {
                    $expectedResponse = $this->unsetGPS((string)json_encode($this->stations[$point]));
                    $wsPoints[] = json_decode($expectedResponse);
                } else {
                    $wsPoints[] = json_decode(FakeGenerator::generateOneFake($point));
                }
            }
        }
        if (count($wsPoints) == 1) {
            $wsPoints = $wsPoints[0];
        }

        $expectedResponse = json_encode($wsPoints) ?: "";

        return new MockResponse($expectedResponse);
    }

    private function unsetGPS(string $jsonWithGps): string
    {
        $jsonWithoutGPS = json_decode($jsonWithGps);
        if ($jsonWithoutGPS instanceof \stdClass) {
            unset($jsonWithoutGPS->request->gps);
        }
        return (string)json_encode($jsonWithoutGPS, JSON_PRETTY_PRINT);
    }

    private static function generateOneFake(string $query): string
    {
        $pattern = '/^[-]?[0-9]+(\.[0-9]+)?,[-]?[0-9]+(\.[0-9]+)?$/';

        if (!preg_match($pattern, $query)) {
            throw new \Exception(
                "La chaîne '$query' ne respecte pas le format 'latitude,longitude' Ex: '1.234,5.678'."
            );
        }
        list($latitude,$longitude) = explode(",", $query);
        $lat = round(floatval($latitude), 2);
        $lon = round(floatval($longitude), 2);
        return <<<EOS
        {
            "request": {
            "type": "LatLon",
            "query": "Lat $lat and Lon $lon",
            "language": "en",
            "unit": "m"
            },
            "location": {
            "name": "FAKE",
            "country": "France",
            "region": "Ile-de-France",
            "lat": "$lat",
            "lon": "$lon",
            "timezone_id": "Europe/Paris",
            "localtime": "2023-06-21 16:14",
            "localtime_epoch": 1687364040,
            "utc_offset": "2.0"
            },
            "current": {
            "observation_time": "02:14 PM",
            "temperature": 26,
            "weather_code": 116,
            "weather_icons": [
                "https://cdn.worldweatheronline.com/images/wsymbols01_png_64/wsymbol_0002_sunny_intervals.png"
            ],
            "weather_descriptions": [
                "Partly cloudy"
            ],
            "wind_speed": 9,
            "wind_degree": 70,
            "wind_dir": "ENE",
            "pressure": 1018,
            "precip": 0,
            "humidity": 51,
            "cloudcover": 50,
            "feelslike": 26,
            "uv_index": 8,
            "visibility": 10,
            "is_day": "yes"
            }
        }
        EOS;
    }
}
