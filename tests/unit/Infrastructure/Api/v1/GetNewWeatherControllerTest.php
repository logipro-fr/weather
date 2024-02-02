<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Infrastructure\Api\v1\Symfony\GetNewWeatherController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;
use Weather\Tests\Features\FakeWeatherApi;

use function Safe\json_encode;

class GetNewWeatherControllerTest extends TestCase
{
    public function testCreate(): void
    {
        $route = new GetNewWeatherController(new WeatherInfoRepositoryInMemory(), new FakeWeatherApi());
        $this->assertInstanceOf(GetNewWeatherController::class, $route);
    }

    public function testExecuteOnOne(): void
    {
        $api = new FakeWeatherApi();
        $route = new FakeGetNewWeatherController($api);

        $query = [
            "points" => '2.1,40.531',
            "date" => "2024-01-01 12:30"
        ];

        $request = new Request($query);

        $response = $route->getWeatherFromApi($request);

        $target = [new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            $api->getLastReturnFromPoint()->getData(),
            false,
            $api->getLastReturnFromPoint()->getId()
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
            "date" => "2024-01-02 12:30"
        ];

        $request = new Request($query);

        $response = $route->getWeatherFromApi($request);

        $target = [
            new WeatherInfo(
                new Point(2.1, 40.531),
                DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-02 12:30"),
                $api->getLastReturnFromMultiplePoints()[0]->getData(),
                false,
                $api->getLastReturnFromMultiplePoints()[0]->getId()
            ),
            new WeatherInfo(
                new Point(5.652, 41.666),
                DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-02 12:30"),
                $api->getLastReturnFromMultiplePoints()[1]->getData(),
                false,
                $api->getLastReturnFromMultiplePoints()[1]->getId()
            )
        ];
        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testBadDate(): void
    {
        $query = [
            "points" => '2.1,40.531;5.652,41.666',
            "date" => "01/02/2024 12"
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"date format invalid, should ' .
            'look like \"YYYY-MM-DD hh:mm:ss\""}';

        $request = new Request($query);
        $route = new FakeGetNewWeatherController(new FakeWeatherApi());

        $response = $route->getWeatherFromApi($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testBadPoint(): void
    {
        $query = [
            "points" => '2.1,40.531&5.652,41.666',
            "date" => "2024-02-01 12:30"
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"point format invalid, should look like ' .
            '\"45.043,3.883;48.867,2.333\""}';

        $request = new Request($query);
        $route = new FakeGetNewWeatherController(new FakeWeatherApi());

        $response = $route->getWeatherFromApi($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoDate(): void
    {
        $query = [
            "points" => '2.1,40.531;5.652,41.666'
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"no date given"}';

        $request = new Request($query);
        $route = new FakeGetNewWeatherController(new FakeWeatherApi());

        $response = $route->getWeatherFromApi($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoPoint(): void
    {
        $query = [
            "date" => "2024-02-01 12:30"
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"no points given"}';

        $request = new Request($query);
        $route = new FakeGetNewWeatherController(new FakeWeatherApi());

        $response = $route->getWeatherFromApi($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
