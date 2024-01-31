<?php

namespace Weather\Test\Infrastructure\External\WeatherStack;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpClient\HttpClient;
use Weather\Domain\Model\Weather\Point;
use Weather\Infrastructure\External\WeatherStack\WeatherStackAPI;

class WeatherStackAPITest extends TestCase
{
    public function testGetFromPointsOld(): void
    {
        $falseClient = $this->createMock(HttpClient::class);
        $apikey = null;
        $api = new WeatherStackAPI($apikey, $falseClient);

        $falseClient->method("request")->willReturn("{}"); // TODO fill response

        $points = [new Point(0,0), new Point(10,10)];
        $date = new DateTimeImmutable("2024-01-01 12:00");

        $infos = $api->getFromPoints($points, $date);

        $this->assertEquals($points[0], $infos[0]->getPoint());
        $this->assertEquals($points[1], $infos[1]->getPoint());
        $this->assertEquals($date, $infos[0]->getDate());
        $this->assertEquals($date, $infos[1]->getDate());
        $this->assertIsString($infos[0]->getData());
    }
}
