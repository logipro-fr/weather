<?php

namespace Weather\Tests\WeatherStack\Application\Service\GetCurrentWeather;

use Weather\Application\Share\PresenterObject;
use Weather\Share\Domain\Point;
use Weather\WeatherStack\Application\Service\GetCurrentWeather\GetCurrentWeather;
use Weather\WeatherStack\Application\Service\GetCurrentWeather\GetCurrentWeatherRequest;
use Weather\WeatherStack\Application\Service\GetCurrentWeather\GetCurrentWeatherResponse;
use Weather\WeatherStack\Application\Service\WeatherAPIInterface;
use PHPUnit\Framework\TestCase;

class GetCurrentWeatherTest extends TestCase
{
    public function testExecuteOneHotPoint(): void
    {
        $hotpoints = [
            new Point(49.003, 2.537)
        ];

        $api = $this->createMock(WeatherAPIInterface::class);
        $api->method('getJsonCurrentWeather')->willReturn([
            "49.003,2.537" => '{ "meteo": "data" }'
        ]);
        $api->method('getLastRequestNumber')->willReturn(1);

        $presenter = new PresenterObject();
        $service = new GetCurrentWeather($presenter, $api);
        $request = new GetCurrentWeatherRequest(
            $hotpoints
        );
        $service->execute($request);

        $response = $presenter->read();
        $this->assertInstanceOf(GetCurrentWeatherResponse::class, $response);

        $this->assertJson($response->weatherHotPoints["49.003,2.537"]);
        $this->assertEquals(1, $response->report->requestRealized);
        $this->assertEquals(1, $response->report->hotpointNumber);
        $this->assertStringMatchesFormat("%d-%d-%d %d:%d", $response->report->requestedAt->format("Y-m-d H:i"));
        $this->assertStringMatchesFormat("%d-%d-%d %d:%d", $response->report->finishedAt->format("Y-m-d H:i"));
    }
}
