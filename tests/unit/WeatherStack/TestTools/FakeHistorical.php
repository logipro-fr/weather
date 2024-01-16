<?php

namespace Weather\Tests\WeatherStack\TestTools;

use Closure;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\file_get_contents;

class FakeHistorical
{
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
        $params = $this->getParams($url);

        $expectedResponse = "";
        if (isset($params['historical_date'])) {
            $historicalDate = is_string($params['historical_date']) ? strval($params['historical_date']) : "";
            list($year, $month, $day) = explode("-", $historicalDate);
            $queryParam = is_string($params['query']) ? strval($params['query']) : "";

            $gpsLocations = explode(";", $queryParam);
            $count = 0;
            foreach ($gpsLocations as $gps) {
                $fullFilename = __DIR__ . "/resources/historical/$year/$month/$day/" . $gps . '.json';
                if (!is_file($fullFilename)) {
                    $expectedResponse .=
                        '{"success":false,"error":{"code":615,"type":"request_failed",' .
                        '"info":"Your API request failed. Please try again or contact support."}}' . ",";
                } else {
                    $expectedResponse .= (string)file_get_contents($fullFilename) . ",";
                }
                $count++;
            }
            $expectedResponse = trim($expectedResponse, ",");
            if ($count > 1) {
                $expectedResponse = "[\n" . $expectedResponse . "\n]";
            }
        }
        return new MockResponse($expectedResponse);
    }

    /**
     *
     * @return array<string,mixed>
     */
    private function getParams(string $url): array
    {
        $parsedUrl = strval(parse_url($url, PHP_URL_QUERY));
        parse_str($parsedUrl, $params);

        /** @var array<string,mixed> */
        return $params;
    }
}
