<?php

namespace Weather\WeatherStack\Application\Service\GetCurrentWeather;

use Weather\Application\Share\PresenterInterface;
use Weather\WeatherStack\Application\Service\WeatherAPIInterface;
use DateTimeImmutable;

class GetCurrentWeather
{
    public function __construct(private PresenterInterface $presenter, private WeatherAPIInterface $weatherApi)
    {
    }

    public function execute(GetCurrentWeatherRequest $request): void
    {
        $requestedAt = new DateTimeImmutable();
        $weatherHotpoints = $this->weatherApi->getJsonCurrentWeather($request->hotpoints);
        $finishedAt = new DateTimeImmutable();

        $report = new \stdClass();
        $report->requestRealized = $this->weatherApi->getLastRequestNumber();
        $report->hotpointNumber = count($weatherHotpoints);
        $report->requestedAt = $requestedAt;
        $report->finishedAt = $finishedAt;
        $response = new GetCurrentWeatherResponse($report, $weatherHotpoints);
        $this->presenter->write($response);
    }
}
