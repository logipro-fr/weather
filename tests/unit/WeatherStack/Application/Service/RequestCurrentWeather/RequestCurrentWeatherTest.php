<?php

namespace Weather\Tests\WeatherStack\Application\Service\RequestCurrentWeather;

use Weather\Application\Share\PresenterObject;
use Weather\Share\Domain\Point;
use Weather\Tests\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherBuilder;
use Weather\WeatherStack\Application\Service\CurrentWeatherApiInterface;
use Weather\WeatherStack\Application\Service\RequestCurrentWeather\RequestCurrentWeather;
use Weather\WeatherStack\Application\Service\RequestCurrentWeather\RequestCurrentWeatherRequest;
use Weather\WeatherStack\Application\Service\RequestCurrentWeather\RequestCurrentWeatherResponse;
use Weather\WeatherStack\Application\Service\WeatherAPIInterface;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeather;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherRepositoryInterface;
use Weather\WeatherStack\Infrastructure\Persistence\CurrentWeather\CurrentWeatherRepositoryInMemory;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class RequestCurrentWeatherTest extends TestCase
{
    private CurrentWeatherRepositoryInterface $repository;

    protected function setUp(): void
    {
        $this->repository = new CurrentWeatherRepositoryInMemory();
    }

    public function testExecuteOneLocation(): void
    {
        $hotpoints = [
            new Point(49.003, 2.537)
        ];

        $api = $this->createMock(CurrentWeatherApiInterface::class);
        $api->method('getCurrentWeathers')->willReturn([
            CurrentWeatherBuilder::aCurrentWeather()
                ->withRequestedAt(new DateTimeImmutable())
                ->build()
        ]);
        $api->method('getRealisedRequest')->willReturn(1);

        $service = new RequestCurrentWeather(new PresenterObject(), $api, $this->repository);
        $request = new RequestCurrentWeatherRequest(
            $hotpoints
        );
        $service->execute($request);

        /** @var RequestCurrentWeatherResponse $response */
        $response = $service->readResponse();

        $this->assertEquals(1, count($response->weatherHotPoints));
        $this->assertEquals(1, $response->report->requestRealized);
        $this->assertEquals(1, $response->report->hotpointNumber);
        $this->assertStringMatchesFormat("%d-%d-%d %d:%d", $response->report->requestedAt->format("Y-m-d H:i"));
        $this->assertStringMatchesFormat("%d-%d-%d %d:%d", $response->report->finishedAt->format("Y-m-d H:i"));

        $result = $this->repository->findRequestedAt($response->report->requestedAt, $response->report->finishedAt);
        $this->assertEquals(1, count($result));
    }
}
