<?php

namespace Weather\Tests\WeatherStack\TestTools;

use Closure;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

use function Safe\file_get_contents;
use function SafePHP\strval;

class FakeCurrent
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
        $gpsLocations = explode(";", strval($params['query']));
        $expectedResponses = [];

        foreach ($gpsLocations as $gps) {
            $fullFilename = __DIR__ . "/resources/current/" . $gps . '.json';

            if (!is_file($fullFilename)) {
                $errorResponse = [
                    "success" => false,
                    "error" => [
                        "code" => 615,
                        "type" => "request_failed",
                        "info" => "Your API request failed. Please try again or contact support."
                    ]
                ];
                $expectedResponses[] = json_encode($errorResponse);
            } else {
                $expectedResponses[] = file_get_contents($fullFilename);
            }
        }

        $response = count($expectedResponses) > 1 ?
            "[\n" . implode(",\n", $expectedResponses) . "\n]" :
            strval($expectedResponses[0]);
        return new MockResponse($response);
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
