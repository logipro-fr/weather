<?php

namespace Weather\WeatherStack;

use Weather\Share\Domain\Point;
use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Application\Service\CurrentWeatherApiInterface;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Weather\WeatherStack\Exceptions\APIErrorException;
use Weather\WeatherStack\Infrastructure\Tools\SplitQuery;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class CurrentWeatherApi implements CurrentWeatherApiInterface
{
    private HttpClientInterface $httpClient;

    private int $realizedRequestInBulk = 0;

    private string $weatherStackApiKey;

    public function __construct(?string $weatherStackApiKey = null, ?HttpClientInterface $httpClient = null)
    {
        $this->weatherStackApiKey = strval(
            $weatherStackApiKey ?? getenv("WEATHERSTACK_API") ?: $_ENV['WEATHERSTACK_API']
        );
        $this->httpClient = $httpClient ?? HttpClient::create();
    }

    /**
     * @param array<Point> $hotpoints
     * @return array<CurrentWeather>
     */
    public function getCurrentWeathers(array $hotpoints): array
    {
        $this->realizedRequestInBulk = 0;

        $weathers = $this->requestCurrents($hotpoints);

        $currentWeathers = [];
        $requestAt = new DateTimeImmutable();

        $index = 0;
        foreach ($weathers as $weather) {
            $locationTime = new LocationTime(
                $hotpoints[$index]->getLatitude(),
                $hotpoints[$index]->getLongitude(),
                $requestAt,
            );
            $currentWeathers[] = new CurrentWeather(
                new CurrentWeatherId(),
                $locationTime,
                json_encode($weather)
            );
                $index++;
        }
        return $currentWeathers;
    }

    /**
     * @param array<Point> $locations
     * @return array<string>
     * @throws APIErrorException
     */
    private function requestCurrents(array $locations): array
    {
        $query = $this->getQuery($locations);
        $queries = (new SplitQuery())->split($query);
        $result = [];
        foreach ($queries as $partQuery) {
            $jsonWeathers =  $this->requestCurrent($partQuery);
            $weathers = json_decode($jsonWeathers);
            if (is_object($weathers) && isset($weathers->success)) {
                throw new APIErrorException(
                    sprintf(
                        "API Error success '%s', code '%s', type '%s', info '%s'",
                        $weathers->success ? "true" : "false",
                        isset($weathers->error->code) ? $weathers->error->code : "???",
                        isset($weathers->error->type) ? $weathers->error->type : "???",
                        isset($weathers->error->info) ? $weathers->error->info : "???",
                    )
                );
            }
            $result = array_merge($result, is_array($weathers) ? $weathers : array($weathers));
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

    private function requestCurrent(string $query): string
    {
        $historicalDate = (new DateTimeImmutable())->format("Y-m-d");
        $url = "https://api.weatherstack.com/historical?access_key=$this->weatherStackApiKey&" .
            "query=$query&units=m&historical_date=$historicalDate&hourly=1&interval=1";
        $options = [];
        $response = $this->httpClient->request('GET', $url, $options);
        $this->realizedRequestInBulk++;

        return $response->getContent();
    }

    public function getRealisedRequest(): int
    {
        return $this->realizedRequestInBulk;
    }
}
