<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Infrastructure\Api\v1\GetNewWeatherController;
use Weather\Infrastructure\Api\v1\HelloWorldController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;
use Weather\Tests\Features\FakeWeatherApi;

use function Safe\json_decode;
use function Safe\json_encode;

class GetNewWeatherControllerTest extends TestCase
{
    public function testCreate(): void
    {
        $route = new GetNewWeatherController(new WeatherInfoRepositoryInMemory());
        $this->assertInstanceOf(GetNewWeatherController::class, $route);
    }

    public function testExecuteOnOne(): void
    {
        $api = new FakeWeatherApi();
        $route = new FakeGetNewWeatherController($api);

        $query = [
            "points" => '2.1,40.531',
            "date" => "2024-01-01 12:30:00.000000"
        ];

        $request = new Request($query);

        $response = $route->getWeatherFromApi($request);

        $target = [new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            $api->getLastReturnFromPoint()->getData()
        )
        ];
        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
    public function testExecuteOnTwo(): void
    {
        $api = new FakeWeatherApi();
        $route = new FakeGetNewWeatherController($api);

        $query = [
            "points" => '2.1,40.531;5.652,41.666',
            "date" => "2024-01-02 12:30:10.153684"
        ];

        $request = new Request($query);

        $response = $route->getWeatherFromApi($request);

        $target = [
            new WeatherInfo(
                new Point(2.1, 40.531),
                DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-02 12:30:10.153684"),
                $api->getLastReturnFromMultiplePoints()[0]->getData()
            ),
            new WeatherInfo(
                new Point(5.652, 41.666),
                DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-02 12:30:10.153684"),
                $api->getLastReturnFromMultiplePoints()[1]->getData()
            )
        ];
        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}