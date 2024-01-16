<?php

namespace Weather\Tests\WeatherStack;

use Weather\Share\Domain\Point;
use Weather\Share\Domain\LocationTime;
use Weather\Tests\WeatherStack\TestTools\FakeHistorical;
use Weather\WeatherStack\HistoricalWeatherApi;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class HistoricalWeatherAPITest extends TestCase
{
    private string $apiKey = "apiWeatherStackKey";

    private HistoricalWeatherApi $api;

    protected function setUp(): void
    {
        $fake = new FakeHistorical();
        $httpClient = $fake->getMockHttpClient();

        $this->api = HistoricalWeatherApi::create($this->apiKey, $httpClient);
    }

    public function testHistoricalOneLocation(): void
    {
        $historicalDate = DateTimeImmutable::createFromFormat("Y/m/d H", "2023/01/25 10");

        $fake = new FakeHistorical();
        $httpClient = $fake->getMockHttpClient();

        $this->api = HistoricalWeatherApi::create($this->apiKey, $httpClient);

        $history = $this->api->getHistoricalWeather(new LocationTime(45.033, 3.883, $historicalDate));

        $this->assertEquals(new Point(45.033, 3.883), $history->getId()->getPoint());
    }

    public function testHistoricaOneLocation(): void
    {
        $historicalDate = DateTimeImmutable::createFromFormat("Y/m/d H", "2023/01/25 10");

        $history = $this->api->getHistoricalWeather(new LocationTime(45.033, 3.883, $historicalDate));

        $this->assertEquals(new Point(45.033, 3.883), $history->getId()->getPoint());
    }

    public function testHistoricalTwoHotPoint(): void
    {
        $historicalDate = DateTimeImmutable::createFromFormat("Y/m/d H", "2023/01/25 10");

        $history1 = $this->api->getHistoricalWeather(new LocationTime(45.033, 3.883, $historicalDate));
        $history2 = $this->api->getHistoricalWeather(new LocationTime(44.039, 4.348, $historicalDate));

        $this->assertEquals(new Point(45.033, 3.883), $history1->getId()->getPoint());
        $this->assertEquals(new Point(44.039, 4.348), $history2->getId()->getPoint());
    }

    public function testCreateWithSymfonyHttpClient(): void
    {
        $api = $this->api = HistoricalWeatherApi::create($this->apiKey);
        $this->assertInstanceOf(HistoricalWeatherApi::class, $api);
    }
}
