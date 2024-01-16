<?php

namespace Weather\WeatherStack;

use AccidentPrediction\Prediction\Domain\Model\HotPoint\HotPoint;
use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Application\Service\HistoricalWeatherAPIInterface;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Exceptions\APIErrorException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Safe\json_decode;
use function Safe\json_encode;

class HistoricalWeatherApi implements HistoricalWeatherAPIInterface
{
    private function __construct(
        private string $weatherStackApiKey,
        private HttpClientInterface $httpClient
    ) {
    }

    public static function create(
        string $weatherStackApiKey,
        ?HttpClientInterface $httpClient = null
    ): HistoricalWeatherApi {
        if ($httpClient === null) {
            $httpClient = HttpClient::create();
        }
        return new HistoricalWeatherApi($weatherStackApiKey, $httpClient);
    }


    public function getHistoricalWeather(LocationTime $locationTime): HistoricalDay
    {
        $weathers = $this->requestHistory($locationTime);

        return new HistoricalDay(
            new HistoricalDayId(
                $locationTime->getPoint(),
                $locationTime->getTime()
            ),
            json_encode($weathers[0]),
        );
    }
    /**
     *
     * @return array<mixed>
     * @throws APIErrorException
     */
    private function requestHistory(LocationTime $locationTime): array
    {
        $query = $this->getQuery([$locationTime]);
        $jsonWeathers =  $this->basicRequestHistorical($query, $locationTime->getTime()->format("Y-m-d"));
        $weathers = json_decode($jsonWeathers);
        if (is_object($weathers) && isset($weathers->success)) {
            throw new APIErrorException(
                sprintf(
                    "API Error success '%s', code '%s', type '%s', info '%s'",
                    $weathers->success,
                    isset($weathers->code) ? $weathers->code : "???",
                    isset($weathers->type) ? $weathers->type : "???",
                    isset($weathers->info) ? $weathers->info : "???",
                )
            );
        }
        return is_array($weathers) ? $weathers : array($weathers);
    }

    /**
     * @param array<HotPoint|LocationTime> $hotPoints
     */
    private function getQuery(array $hotPoints): string
    {
        $query = "";
        foreach ($hotPoints as $hotpoint) {
            $query .= $hotpoint->getLatitude() . "," . $hotpoint->getLongitude() . ";";
        }
        return rtrim($query, ";");
    }

    private function basicRequestHistorical(string $query, string $historicalDate): string
    {
        $url = "https://api.weatherstack.com/historical?access_key=$this->weatherStackApiKey&" .
            "query=$query&units=m&historical_date=$historicalDate&hourly=1&interval=1";
        $options = [];
        $response = $this->httpClient->request('GET', $url, $options);

        return $response->getContent();
    }
}
