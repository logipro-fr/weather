<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;
use Weather\Infrastructure\Api\v1\Symfony\LegacyFileController;
use Weather\Infrastructure\Api\v1\Symfony\RequestController;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

use function Safe\scandir;

class ImportLegacyControllerTest extends TestCase
{
    private WeatherInfoRepositoryInterface $repos;
    private RequestController $route;
    public function setUp(): void
    {
        $this->repos = new WeatherInfoRepositoryInMemory();
        $this->route = new LegacyFileController($this->repos);
    }

    public function testCreate(): void
    {
        $this->assertInstanceOf(LegacyFileController::class, $this->route);
    }

    public function testExecute(): void
    {

        $query = [
            "path" => './tests/Features/data/2024/01/02/2024-01-02-11-08.json',
        ];
        $request = new Request($query);

        $target = ["success" => true,"data" => ["size" => 2500],"errorCode" => null,"message" => null];

        $response = $this->route->execute($request);

        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testNoPath(): void
    {
        $query = [
            "date" => "2024-02-01 12:30"
        ];

        $target = '{"success":false,"data":null,"errorCode":"invalid_argument","message":"no \"path\" given"}';

        $request = new Request($query);

        $response = $this->route->execute($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testBadPath(): void
    {
        $query = [
            "path" => "according/to/all/known/laws/of/aviation"
        ];

        $target = '{"success":false,"data":null,"errorCode":"invalid_argument",' .
            '"message":"according\/to\/all\/known\/laws\/of\/aviation is not a valid file or directory"}';

        $request = new Request($query);

        $response = $this->route->execute($request);
        $this->assertEquals($target, $response->getContent());
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
