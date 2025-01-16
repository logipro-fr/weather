<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Symfony\LegacySqlController;
use Weather\Infrastructure\Api\v1\Symfony\RequestController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class ImportLegacySQLControllerTest extends TestCase
{
    private WeatherInfoRepositoryInterface $repos;
    private RequestController $route;
    public function setUp(): void
    {
        $this->repos = new WeatherInfoRepositoryInMemory();
        $this->route = new LegacySqlController($this->repos);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(LegacySqlController::class, $this->route);
    }

    public function testExecute(): void
    {

        $query = [
            "db" => 'mysql:host=weather-mariadb:3306;dbname=weather',
            "table" => "currentweathers",
            "user" => "weather",
            "pwd" => "weather"
        ];
        $request = new Request($query);

        $target = ["success" => true,"data" => ["size" => 1000],"errorCode" => null,"message" => null];

        $response = $this->route->execute($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoDB(): void
    {
        $query = [
            "table" => "currentweathers",
            "user" => "weather",
            "pwd" => "weather"
        ];

        $target = '{"success":false,"data":null,"errorCode":"invalid_argument","message":"no database \"db\" given"}';

        $request = new Request($query);

        $response = $this->route->execute($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoTable(): void
    {
        $query = [
            "db" => "currentweathers",
            "user" => "weather",
            "pwd" => "weather"
        ];

        $target = '{"success":false,"data":null,"errorCode":"invalid_argument","message":"no \"table\" given"}';

        $request = new Request($query);

        $response = $this->route->execute($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoUser(): void
    {
        $query = [
            "db" => "currentweathers",
            "table" => "weather",
            "pwd" => "weather"
        ];

        $target = '{"success":false,"data":null,"errorCode":"invalid_argument","message":"no \"user\" given"}';

        $request = new Request($query);

        $response = $this->route->execute($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testDBError(): void
    {
        $query = [
            "db" => "sd",
            "table" => "weather",
            "user" => "weather"
        ];

        $target = '{"success":false,"data":null,"errorCode":"database_error_exception",' .
            '"message":"PDO::__construct(): Argument #1 ($dsn) must be a valid data source name"}';

        $request = new Request($query);

        $response = $this->route->execute($request);

        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
