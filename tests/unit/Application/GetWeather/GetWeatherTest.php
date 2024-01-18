<?php

namespace Weather\Tests\Application\GetWeather;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\APIs\WeatherApiInterface;
use Weather\Application\GetWeather\GetWeather;
use Weather\Application\GetWeather\GetWeatherRequest;
use Weather\Application\GetWeather\GetWeatherResponse;
use Weather\Application\Presenter\PresenterObject;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Infrastructure\Persistence\Weather\WeatherInfoRepositoryInMemory;

class GetWeatherTest extends TestCase
{
    public function testExecute(): void
    {
        //setup
        $point = new Point(0, 0);
        $date = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $info = new WeatherInfo($point, $date, "{\"weather\":\"great\"}");

        $api = $this->createMock(WeatherApiInterface::class);
        $api->method("getFromPoints")->willReturn([$info]);

        $repository = new WeatherInfoRepositoryInMemory();
        $presenter = new PresenterObject();

        $service = new GetWeather($presenter, $api, $repository);
        $request = new GetWeatherRequest(array($point), $date);

        //action
        $service->execute($request);
        $response = $presenter->read();

        //tests
        $this->assertInstanceOf(GetWeatherResponse::class, $response);
        $this->assertEquals("[" . $info->getData() . "]", $response->getData());
        $this->assertEquals($info, $repository->findById($info->getId()));
    }

    public function testExecuteTwice(): void
    {
        //setup

        $pointA = new Point(0, 0);
        $dateA = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:00");
        $infoA = new WeatherInfo($pointA, $dateA, "{\"weather\":\"great\"}");

        $requestA = new GetWeatherRequest(array($pointA), $dateA);

        $pointB = new Point(1, 1);
        $dateB = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-02 12:00");
        $infoB = new WeatherInfo($pointB, $dateB, "{\"weather\":\"great\"}");

        $requestB = new GetWeatherRequest(array($pointB), $dateB);

        $presenter = new PresenterObject();
        $api = $this->createMock(WeatherApiInterface::class);
        $repository = new WeatherInfoRepositoryInMemory();

        $service = new GetWeather($presenter, $api, $repository);


        //action
        $api->method("getFromPoints")->willReturn([$infoA], [$infoB]);
        $service->execute($requestA);
        $responseA = $presenter->read();

        $service->execute($requestB);
        $responseB = $presenter->read();

        //tests
        $this->assertInstanceOf(GetWeatherResponse::class, $responseA);
        $this->assertEquals("[" . $infoA->getData() . "]", $responseA->getData());
        $this->assertEquals($infoA, $repository->findById($infoA->getId()));

        $this->assertInstanceOf(GetWeatherResponse::class, $responseB);
        $this->assertEquals("[" . $infoB->getData() . "]", $responseB->getData());
        $this->assertEquals($infoB, $repository->findById($infoB->getId()));
    }

    public function testExecuteOnMultiplePoints(): void
    {
        //setup
        $points = $this->pointFactory(4);
        $dates = $this->dateFactory(1);
        $infos = $this->infoFactory($points, $dates);

        $api = $this->createMock(WeatherApiInterface::class);
        $api->method("getFromPoints")->willReturn($infos);

        $repository = new WeatherInfoRepositoryInMemory();
        $presenter = new PresenterObject();

        $service = new GetWeather($presenter, $api, $repository);
        $request = new GetWeatherRequest($points, $dates[0]);

        //action
        $service->execute($request);
        $response = $presenter->read();

        //tests
        $this->assertInstanceOf(GetWeatherResponse::class, $response);
        $this->assertEquals($this->infoArrayToString($infos), $response->getData());
        foreach ($infos as $info) {
            $this->assertEquals($info, $repository->findById($info->getId()));
        }
    }

    /**
     * @return array<Point>
     */
    private function pointFactory(int $amount): array
    {
        $res = [];
        $amount = $amount > 0 ? $amount : 1;
        foreach (range(0, $amount - 1) as $n) {
            array_push($res, new Point($n, $n));
        }
        return $res;
    }

    /**
     * @return array<DateTimeImmutable>
     */
    private function dateFactory(int $amount = 1): array
    {
        $res = [];
        $amount = $amount > 0 ?: 1;
        foreach (range(0, $amount - 1) as $n) {
            array_push($res, new DateTimeImmutable(strval(2000 + $n) . "-01-01 12:00"));
        }
        return $res;
    }

    private const POSSIBLE_WEATHERS = ["great", "mid", "bad", "it's raining menu"];

    /**
     * @param array<Point> $points
     * @param array<DateTimeImmutable> $dates
     * @return array<WeatherInfo>
     */
    private function infoFactory(array $points, array $dates): array
    {
        $res = [];
        foreach (range(0, sizeof($points) - 1) as $i) {
            $weatherType = $this::POSSIBLE_WEATHERS[$i % sizeof($this::POSSIBLE_WEATHERS)];
            $weatherString = "{\"weather\":\"" . $weatherType . "\"}";
            $info = new WeatherInfo($points[$i], $dates[$i % sizeof($dates)], $weatherString);
            array_push($res, $info);
        }
        return $res;
    }

    /**
     * @param array<WeatherInfo> $infoArray
     */
    private function infoArrayToString(array $infoArray): string
    {
        $new = array_map(['Weather\Tests\Application\GetWeather\GetWeatherTest','infoToDataCallback'], $infoArray);
        $res = "[" . implode(",", $new) . "]";
        return $res;
    }
    private function infoToDataCallback(WeatherInfo $info): string
    {
        return $info->getData();
    }
}
