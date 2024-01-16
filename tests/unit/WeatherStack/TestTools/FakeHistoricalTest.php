<?php

namespace Weather\Tests\WeatherStack\TestTools;

use PHPUnit\Framework\TestCase;

use function Safe\file_get_contents;
use function Safe\json_decode;

class FakeHistoricalTest extends TestCase
{
    public function testOnePoint20230125(): void
    {
        $fake = new FakeHistorical();

        $url = "https://api.weatherstack.com/historical?access_key=1234&" .
            "query=45.033,3.883&units=m&historical_date=2023-01-25&hourly=1&interval=1";

        $response = $fake->getMockHttpClient()->request("GET", $url);

        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testUrlFormat(): void
    {
        $fake = new FakeHistorical();

        $url = "https://api.weatherstack.com/historical?access_key=1234&" .
            "query=45.033,3.883&units=m&hourly=1&interval=1";

        $response = $fake->getMockHttpClient()->request("GET", $url);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testOnePoint20230126(): void
    {
        $fake = new FakeHistorical();

        $url = "https://api.weatherstack.com/historical?access_key=1234&" .
            "query=44.039,4.348&units=m&historical_date=2023-01-26&hourly=1&interval=1";

        $response = $fake->getMockHttpClient()->request("GET", $url);

        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testTwoPoints20230125(): void
    {
        $fake = new FakeHistorical();

        $url = "https://api.weatherstack.com/historical?access_key=1234&" .
            "query=45.033,3.883;44.039,4.348&units=m&historical_date=2023-01-25&hourly=1&interval=1";

        $response = $fake->getMockHttpClient()->request("GET", $url);

        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());

        $expected = file_get_contents(
            __DIR__ . '/resources/historical/historical-2points-true-result-45.033,3.883-44.039,4.348-25.json'
        );
        $content = $response->getContent();

        $this->assertEquals(json_decode($expected), json_decode($content));

        /** @var array<mixed> */
        $twoPoints = json_decode($content);
        $this->assertEquals(2, count($twoPoints));
    }
}
