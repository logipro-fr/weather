<?php

namespace Weather\WeatherStack;

use Weather\Infrastructure\PredictiveModel\PredictiveModelTools;
use Weather\Share\Domain\Point;
use Weather\WeatherStack\Application\Service\WeatherAPIInterface;
use Weather\WeatherStack\Infrastructure\Tools\SplitQuery;
use stdClass;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class WeatherStackApi implements WeatherAPIInterface
{
    private int $lastRequestNumber = 0;

    private function __construct(private string $weatherStackApiKey, private HttpClientInterface $httpClient)
    {
    }

    public static function create(
        ?string $weatherStackApiKey = null,
        HttpClientInterface $httpClient = null
    ): WeatherStackApi {
        $weatherStackApiKey = strval(
            $weatherStackApiKey == null ?
            getenv('WEATHERSTACK_API') : $weatherStackApiKey
        );
        if ($httpClient === null) {
            $httpClient = HttpClient::create();
        }
        return new WeatherStackApi($weatherStackApiKey, $httpClient);
    }

    /**
     * @param array<Point> $hotpoints
     * @return array<string,string>
     */
    public function getJsonCurrentWeather(array $hotpoints): array
    {
        $query = $this->getQuery($hotpoints);
        $response = $this->request($query);
        $result = [];
        foreach ($response as $weatherPoint) {
            $key = $weatherPoint->request->gps->on_Latitude . "," . $weatherPoint->request->gps->on_Longitude;

            $result[$key] = (string)json_encode($weatherPoint, JSON_PRETTY_PRINT);
        }
        return $result;
    }

    /**
     * @param array<Point> $hotPoints
     */
    private function getQuery(array $hotPoints): string
    {
        $query = "";
        foreach ($hotPoints as $hotpoint) {
            $query .= $hotpoint->getLatitude() . "," . $hotpoint->getLongitude() . ";";
        }
        return rtrim($query, ";");
    }

    private function basicRequest(string $query): string
    {
        $url = "https://api.weatherstack.com/current?access_key=$this->weatherStackApiKey&query=$query&units=m";
        $options = [];
        $response = $this->httpClient->request('GET', $url, $options);

        $this->lastRequestNumber += count(explode(";", $query));

        return (string)json_encode(json_decode($response->getContent()));
    }

    /**
     * @return array<int,\stdClass>
     */
    public function request(string $query): array
    {
        $this->lastRequestNumber = 0;

        if ($this->isCoordinatePattern($query)) {
            return $this->getCurrentWeatherForSeveralCoordiantes($query);
        }

        $content = $this->basicRequest($query);
        $this->lastRequestNumber = 1;
        return [(object)json_decode($content)];
    }

    private function isCoordinatePattern(string $query): bool
    {
        $pattern = '/^[-]?\d+(\.\d+)?,[-]?\d+(\.\d+)?$/';
        $points = explode(";", $query);
        foreach ($points as $point) {
            if (!preg_match($pattern, $point)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return array<int,\stdClass>
     */
    private function getCurrentWeatherForSeveralCoordiantes(string $query): array
    {
        $this->lastRequestNumber = 0;

        $minimizedQuery = WeatherStackApi::minimizeQueries($query);
        $queries = (new SplitQuery())->split($minimizedQuery);
        $minimalResult = $this->requestUsingSubQueries($queries);

        $stations = $this->mapStationsByCoordinates($minimalResult);

        $result = [];
        $allQueries = explode(";", $query);
        $cached = WeatherStackTools::constructMapCachePointsToStations();

        foreach ($allQueries as $point) {
            $theResult = $this->getCurrentWeatherByPoint($point, $cached, $stations);
            $result[] = $theResult;
        }

        return $result;
    }

    /**
     * @param array<string> $queries
     * @return array<int,\stdClass>
     */
    private function requestUsingSubQueries(array $queries): array
    {
        $currentWeathers = [];

        foreach ($queries as $subQuery) {
            $jsonContent = $this->basicRequest($subQuery);
            $aCurrentWeather = json_decode($jsonContent);

            if (!is_array($aCurrentWeather)) {
                $aCurrentWeather = [$aCurrentWeather];
            }

            $currentWeathers = array_merge($currentWeathers, $aCurrentWeather);
        }

        return $currentWeathers;
    }

    /**
     * @param array<int,\stdClass> $minimalResult
     * @return array<string,\stdClass>
     */
    private function mapStationsByCoordinates(array $minimalResult): array
    {
        $stations = [];

        foreach ($minimalResult as $index => $current) {
            $key = $current->location->lat . "," . $current->location->lon;
            $stations[$key] = $current;
        }

        return $stations;
    }

    /**
     * @param array<string,string> $cached
     * @param array<string,\stdClass> $stations
     * @return stdClass
     */
    private function getCurrentWeatherByPoint(string $point, array $cached, array $stations): \stdClass
    {
        $theResult = null;

        if (isset($cached[$point]) && isset($stations[$cached[$point]])) {
            $theResult = $this->getCurrentWeather((string) json_encode($stations[$cached[$point]]), $point);
        }

        if ($theResult === null && isset($stations[$point])) {
            $theResult = $this->getCurrentWeather((string) json_encode($stations[$point]), $point);
        }

        if ($theResult === null) {
            $theResult = $this->getCurrentWeather($this->basicRequest($point), $point);
        }

        return $theResult;
    }

    private function getCurrentWeather(string $jsonCurrentWeather, string $point): \stdClass
    {
        $theResult = (object)json_decode($jsonCurrentWeather);
        list($latitude, $longitude) = explode(",", $point);
        $gps = new \stdClass();
        $gps->on_Latitude = floatval($latitude);
        $gps->on_Longitude = floatval($longitude);
        $theResult->request->gps = $gps;
        return $theResult;
    }

    public static function minimizeQueries(string $query): string
    {
        $queryPoints = explode(";", $query);
        $gpsPoints = explode(";", PredictiveModelTools::convertJsonHotPoints2String(
            (string)file_get_contents(__DIR__ . '/resources/list-of-2500-hotpoints.json')
        ));
        /** @var array<string,\stdClass> */
        $weatherStackPoints = json_decode(
            (string)file_get_contents((__DIR__ . "/resources/bulk-2500.json"))
        );
        $cached = WeatherStackTools::constructMapCachePointsToStations($gpsPoints, $weatherStackPoints);
        $shortQuery = "";
        $tempKey = [];
        foreach ($queryPoints as $point) {
            if (isset($cached[$point])) {
                $cachedPoint = $cached[$point];
                if (!in_array($cachedPoint, $tempKey)) {
                    $shortQuery .= $cachedPoint . ";";
                    $tempKey[] = $cachedPoint;
                }
            } else {
                $shortQuery .= $point . ";";
            }
        }
        if ($shortQuery != '') {
            $shortQuery = rtrim($shortQuery, ";");
        }
        return $shortQuery;
    }

    public function requestHistorical(string $query, string $historicalDate): \stdClass
    {
        $content = $this->basicRequestHistorical($query, $historicalDate);

        /** @var \stdClass */
        $result = json_decode($content);
        return $result;
    }

    private function basicRequestHistorical(string $query, string $historicalDate): string
    {
        $url = "https://api.weatherstack.com/historical?access_key=$this->weatherStackApiKey&" .
            "query=$query&units=m&historical_date=$historicalDate&hourly=1&interval=1";
        $options = [];
        $response = $this->httpClient->request('GET', $url, $options);

        return $response->getContent();
    }

    /**
     * @return array<string,\stdClass>
     */
    public function requestHistoricalBulk(string $query, string $historicalDate): array
    {

        $queries = (new SplitQuery())->split($query);

        $result = [];
        foreach ($queries as $subQuery) {
            $jsonContent = $this->basicRequestHistorical($subQuery, $historicalDate);
            $content = json_decode($jsonContent);
            if (!is_array($content)) {
                $content = [$content];
            }
            $result = array_merge($result, $content);
        }
        return $result;
    }

    public function getLastRequestNumber(): int
    {
        return $this->lastRequestNumber;
    }
}
