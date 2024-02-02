<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Symfony\GetExistingWeatherByDatePointController;
use Weather\Infrastructure\Api\v1\Symfony\GetExistingWeatherByIdController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class GetExistingWeatherControllerByIdTest extends TestCase
{
    private WeatherInfoRepositoryInterface $repository;

    public function setUp(): void
    {
        $this->repository = new WeatherInfoRepositoryInMemory();
    }

    public function testCreate(): void
    {
        $route = new GetExistingWeatherByIdController($this->repository);
        $this->assertInstanceOf(GetExistingWeatherByIdController::class, $route);
    }

    public function testExecute(): void
    {

        $query = [
            "id" => 'testID_0123456798abcdef0123456798abcdef',
        ];

        $request = new Request($query);

        $target = ["success"=>true,"data"=>new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            "{}",
            true,
            new WeatherInfoId($query["id"])
        ),"errorCode"=>null,"message"=>null];
        $this->repository->save($target["data"][0]);

        $route = new GetExistingWeatherByIdController($this->repository);
        $response = $route->execute($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoId(): void
    {
        $query = [
            "date" => "2024-02-01 12:30"
        ];

        $target = '{"success":false,"data":null,"errorCode":"invalid_argument","message":"no identifier \"id\" given"}';

        $request = new Request($query);
        $route = new GetExistingWeatherByIdController(new WeatherInfoRepositoryInMemory());

        $response = $route->execute($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
