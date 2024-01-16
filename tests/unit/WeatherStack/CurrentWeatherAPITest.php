<?php

namespace Weather\Tests\WeatherStack;

use Weather\Share\Domain\Point;
use Weather\Share\Domain\LocationTime;
use Weather\Tests\WeatherStack\TestTools\FakeCurrent;
use Weather\WeatherStack\CurrentWeatherApi;
use Weather\WeatherStack\Exceptions\APIErrorException;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class CurrentWeatherAPITest extends TestCase
{
    private string $apiKey = "apiWeatherStackKey";

    private CurrentWeatherApi $api;

    protected function setUp(): void
    {
        $fake = new FakeCurrent();
        $httpClient = $fake->getMockHttpClient();

        $this->api = new CurrentWeatherApi($this->apiKey, $httpClient);
    }

    public function testOneLocation(): void
    {
        $currentWeathers = $this->api->getCurrentWeathers([new Point(48.863, 2.313)]);

        $this->assertEquals(1, count($currentWeathers));

        $requestDate = DateTimeImmutable::createFromFormat("Y/m/d H:i", "2023/01/25 10:14");
        $expectedRequestAt = new LocationTime(48.863, 2.313, $requestDate);
        $this->assertEquals($expectedRequestAt->getPoint(), $currentWeathers[0]->getRequestAt()->getPoint());
        $this->assertEquals(27, $currentWeathers[0]->getCurrent('temperature'));
    }

    public function testAPIErrorException(): void
    {
        $this->expectException(APIErrorException::class);
        $this->expectExceptionMessage("API Error success 'false', code '615', " .
            "type 'request_failed', info 'Your API request failed. Please try again or contact support.'");
        $this->api->getCurrentWeathers([new Point(4, 2)]);
    }

    public function testBulk2PointsOnlyOneQuery(): void
    {
        $currentWeathers = $this->api->getCurrentWeathers([
            new Point(49.003, 2.537),
            new Point(48.863, 2.313),
        ]);

        $this->assertEquals(2, count($currentWeathers));
    }

    public function testBulkSeveralLocationButOnlyOneQuery(): void
    {
        $currentWeathers = $this->api->getCurrentWeathers([
            new Point(49.003, 2.537),
            new Point(48.863, 2.313),
            new Point(45.792, 3.141),
        ]);

        $this->assertEquals(3, count($currentWeathers));
        $this->assertEquals(1, $this->api->getRealisedRequest());
    }
}
