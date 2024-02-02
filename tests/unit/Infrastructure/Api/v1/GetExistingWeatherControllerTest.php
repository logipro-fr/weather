<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Symfony\GetExistingWeatherController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class GetExistingWeatherControllerTest extends TestCase
{
    private WeatherInfoRepositoryInterface $repository;

    public function setUp(): void
    {
        $this->repository = new WeatherInfoRepositoryInMemory();
    }

    public function testCreate(): void
    {
        $route = new GetExistingWeatherController($this->repository);
        $this->assertInstanceOf(GetExistingWeatherController::class, $route);
    }

    public function testExecuteOnId(): void
    {

        $query = [
            "id" => 'testID_0123456798abcdef0123456798abcdef',
        ];

        $request = new Request($query);

        $target = new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}",
            true,
            new WeatherInfoId($query["id"])
        );
        $this->repository->save($target);

        $route = new GetExistingWeatherController($this->repository);
        $response = $route->getWeatherFromApiById($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testExecuteOnDateAndPointImprecise(): void
    {

        $query = [
            "point" => '2.142,40.531',
            "date" => "2024-01-01 12:35"
        ];

        $request = new Request($query);

        $target = new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}",
            true
        );
        $this->repository->save($target);

        $route = new GetExistingWeatherController($this->repository);
        $response = $route->getWeatherFromApiByDateAndPoint($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testExecuteOnDateAndPointPrecise(): void
    {

        $query = [
            "point" => '2.1,40.531',
            "date" => "2024-01-01 12:30",
            "exact" => true
        ];

        $request = new Request($query);

        $target = new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}",
            true
        );
        $this->repository->save($target);

        $route = new GetExistingWeatherController($this->repository);
        $response = $route->getWeatherFromApiByDateAndPoint($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testExecuteOnDateAndPointWithHistory(): void
    {

        $query = [
            "point" => '2.142,40.531',
            "date" => "2024-01-01 12:35"
        ];

        $request = new Request($query);

        $target = new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}",
            true
        );
        $this->repository->save($target);

        $route = new GetExistingWeatherController($this->repository);
        $response = $route->getWeatherFromApiByDateAndPoint($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testExecuteOnDateAndPointPreciseFail(): void
    {

        $query = [
            "point" => '2.142,40.531',
            "date" => "2024-01-01 12:35",
            "exact" => true
        ];

        $request = new Request($query);

        $info = new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}",
            true
        );
        $this->repository->save($info);
        $target = '{"code":404,"type":"weatherinfo_not_found_exception","error":"WeatherInfo of point ' .
            '\"2.142,40.531\" at date 2024-01-01 12:35:00 not found"}';

        $route = new GetExistingWeatherController($this->repository);
        $response = $route->getWeatherFromApiByDateAndPoint($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testExecuteOnDateAndPointHistoricalFail(): void
    {

        $query = [
            "point" => '2.142,40.531',
            "date" => "2024-01-01 12:35",
            "historicalOnly" => true
        ];

        $request = new Request($query);

        $info = new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}"
        );
        $this->repository->save($info);
        $target = '{"code":404,"type":"weatherinfo_not_found_exception","error":"Historical WeatherInfo of ' .
            'point \"2.142,40.531\" at date 2024-01-01 12:35:00 not found"}';

        $route = new GetExistingWeatherController($this->repository);
        $response = $route->getWeatherFromApiByDateAndPoint($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testBadDate(): void
    {
        $query = [
            "point" => '2.1,40.531',
            "date" => "01/02/2024 12"
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"date format invalid, should look like ' .
            '\"YYYY-MM-DD hh:mm:ss\""}';

        $request = new Request($query);
        $route = new GetExistingWeatherController(new WeatherInfoRepositoryInMemory());

        $response = $route->getWeatherFromApiByDateAndPoint($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testBadPoint(): void
    {
        $query = [
            "point" => '2.1,',
            "date" => "2024-02-01 12:30"
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"point format invalid, should look like ' .
            '\"45.043,3.883\""}';

        $request = new Request($query);
        $route = new GetExistingWeatherController(new WeatherInfoRepositoryInMemory());

        $response = $route->getWeatherFromApiByDateAndPoint($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoDate(): void
    {
        $query = [
            "point" => '2.1,40.531'
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"no date given"}';

        $request = new Request($query);
        $route = new GetExistingWeatherController(new WeatherInfoRepositoryInMemory());

        $response = $route->getWeatherFromApiByDateAndPoint($request);
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
        $route = new GetExistingWeatherController(new WeatherInfoRepositoryInMemory());

        $response = $route->getWeatherFromApiByDateAndPoint($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoId(): void
    {
        $query = [
            "date" => "2024-02-01 12:30"
        ];

        $target = '{"code":400,"type":"invalid_argument","error":"no identifier given"}';

        $request = new Request($query);
        $route = new GetExistingWeatherController(new WeatherInfoRepositoryInMemory());

        $response = $route->getWeatherFromApiById($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
