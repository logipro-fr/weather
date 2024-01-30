<?php

namespace Weather\Integration\Infrastructure\Api\v1\Symfony;

use Safe\DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Weather\APIs\WeatherApiInterface;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Tests\Features\FakeWeatherApi;

class GetNewWeatherControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = self::createClient(["debug" => false]);
    }

    public function testExecuteOnOne(): void
    {
        $query = [
            "points" => '2.1,40.531',
            "date" => "2024-01-01 12:30:00"
        ];
        $this->client->request("GET", '/api/v1/weather', $query);
        /** @var FakeWeatherApi $api */
        $api = $this->client->getContainer()->get(WeatherApiInterface::class);

        $target = [new WeatherInfo(
            new Point(2.1, 40.531),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:30"),
            $api->getLastReturnFromPoint()->getData()
        )
        ];
        $response = $this->client->getResponse();
        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }

    public function testExecuteOnTwo(): void
    {

        $query = [
            "points" => '2.1,40.531;5.652,41.666',
            "date" => "2024-01-02 12:30:10"
        ];

        $this->client->request("GET", '/api/v1/weather', $query);


        $response = $this->client->getResponse();
        /** @var FakeWeatherApi $api */
        $api = $this->client->getContainer()->get(WeatherApiInterface::class);

        $target = [
            new WeatherInfo(
                new Point(2.1, 40.531),
                DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2024-01-02 12:30:10"),
                $api->getLastReturnFromMultiplePoints()[0]->getData()
            ),
            new WeatherInfo(
                new Point(5.652, 41.666),
                DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2024-01-02 12:30:10"),
                $api->getLastReturnFromMultiplePoints()[1]->getData()
            )
        ];
        $this->assertEquals(json_encode($target), $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
