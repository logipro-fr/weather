<?php

namespace Weather\Infrastructure\External\WeatherStack;

use Exception;
use Safe\DateTimeImmutable;
use stdClass;
use Weather\Domain\Model\Weather\WeatherInfo;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Weather\Domain\Model\Exceptions\ApiException;
use Weather\Domain\Model\Exceptions\BaseException;
use Weather\Domain\Model\Exceptions\WeatherInfoNotFoundException;
use Weather\Domain\Model\Weather\Point;
use Weather\Infrastructure\External\WeatherApiInterface;
use Weather\Infrastructure\Tools\SplitQuery;

use function Safe\json_decode;
use function Safe\json_encode;

class WeatherStackAPI implements WeatherApiInterface
{
    private const NAME = "WeatherStack";
    private const CURRENT_BACK_MARGIN = 900;
    private const DATE_FORMAT = "Y-m-d H:i";

    private string $weatherStackApiKey;
    private HttpClientInterface $httpClient;

    public function __construct(
        ?string $weatherStackApiKey = null,
        ?HttpClientInterface $httpClient = null
    ) {
        $this->weatherStackApiKey = strval(
            $weatherStackApiKey == null ?
            getenv('WEATHERSTACK_API') : $weatherStackApiKey
        );
        if ($httpClient === null) {
            $this->httpClient = HttpClient::create();
        } else {
            $this->httpClient = $httpClient;
        }
    }

    /**
     * @param array<Point> $points
     * @return array<WeatherInfo>
     */
    public function getFromPoints(array $points, DateTimeImmutable $date): array
    {
        $historicalDate = $date->format("Y-m-d");

        $type = 'currentQuery';
        $past = $date->getTimestamp() < (new DateTimeImmutable())->getTimestamp() - self::CURRENT_BACK_MARGIN;
        if ($past) {
            $type = 'historicalQuery';
        }

        $result = $this->requestBulk($points, $date->format(self::DATE_FORMAT), $type);

        array_map(function ($res, $point) {
            $res->point = $point;
        }, $result, $points);
        return $this->createInfos($result, $past);
    }

    /**
     * @param array<\stdClass> $results
     * @return array<WeatherInfo>
     */
    private function createInfos(array $results, bool $historical): array
    {
        $infos = [];
        /** @var object{"point": Point, "location": object{"localtime": string}} $data */
        foreach ($results as $data) {
            $point = new Point($data->point->getLatitude(), $data->point->getLongitude());
            unset($data->point);
            $newInfo = new WeatherInfo(
                $point,
                new DateTimeImmutable(), //DateTimeImmutable::createFromFormat("Y-m-d H:i", $data->location->localtime),
                json_encode($data),
                $historical
            );
            array_push($infos, $newInfo);
        }
        return $infos;
    }

    private function currentQuery(string $query): string
    {
        $url = "https://api.weatherstack.com/current?access_key=$this->weatherStackApiKey&query=$query&units=m";

        $options = [];
        $response = $this->httpClient->request('GET', $url, $options)->getContent();

        $resObject = json_decode($response);
        if (isset($resObject->success) && $resObject->success == false){
            $this->parseErrorAndThrow($resObject);
        }
        return $response;
    }

    private function historicalQuery(string $query, string $historicalDate): string
    {
        $historicalDate = urlencode($historicalDate);
        $url = "https://api.weatherstack.com/historical?access_key=$this->weatherStackApiKey&" .
            "query=$query&units=m&historical_date=$historicalDate&hourly=1&interval=1";

        $options = [];
        $response = $this->httpClient->request('GET', $url, $options)->getContent();

        $resObject = json_decode($response);
        if (isset($resObject->success) && $resObject->success == false){
            throw new ApiException($resObject->error->type, $resObject->error->code);
        }
        return $response;
    }

    /**
     * @param object{"success": bool, "error": object}
     */
    private function parseErrorAndThrow(stdClass $errorResponse): void{
        switch ($errorResponse->error->type){
            case "invalid_access_key":
                $code = 401;
                break;
            default:
                $code = $errorResponse->error->code;       
        }
        throw new ApiException(json_encode($errorResponse->error), $code);
    } 

    /**
     * @param array<Point> $points
     * @return array<\stdClass>
     */
    private function requestBulk(array $points, string $historicalDate, string $request): array
    {
        $query = implode(";", $points);
        $queries = (new SplitQuery())->split($query);

        $result = [];
        foreach ($queries as $subQuery) {
            $jsonContent = $this->$request($subQuery, $historicalDate);
            $content = json_decode($jsonContent);
            if (!is_array($content)) {
                $content = [$content];
            }
            $result = array_merge($result, $content);
        }
        return $result;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public static function create(
        ?string $weatherStackApiKey = null,
        HttpClientInterface $httpClient = null
    ): WeatherApiInterface {
        $weatherStackApiKey = strval(
            $weatherStackApiKey == null ?
            getenv('WEATHERSTACK_API') : $weatherStackApiKey
        );
        if ($httpClient === null) {
            $httpClient = HttpClient::create();
        }
        return new WeatherStackAPI($weatherStackApiKey, $httpClient);
    }
}
