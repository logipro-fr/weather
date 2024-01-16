<?php

namespace Weather\WeatherStack\Application\Service\RequestCurrentWeather;

use Weather\Application\Share\PresenterInterface;
use Weather\WeatherStack\Application\Service\CurrentWeatherApiInterface;
use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherRepositoryInterface;
use Safe\DateTimeImmutable;

class RequestCurrentWeather
{
    public function __construct(
        private PresenterInterface $presenter,
        private CurrentWeatherApiInterface $weatherApi,
        private CurrentWeatherRepositoryInterface $repository
    ) {
    }

    public function execute(RequestCurrentWeatherRequest $request): void
    {
        $requestedAt = new DateTimeImmutable();
        $weatherHotpoints = $this->weatherApi->getCurrentWeathers($request->hotpoints);
        $finishedAt = new DateTimeImmutable();

        foreach ($weatherHotpoints as $hotpointCurrentWeather) {
            $this->repository->add($hotpointCurrentWeather);
        }

        $report = new Report(
            $this->weatherApi->getRealisedRequest(),
            count($weatherHotpoints),
            $requestedAt,
            $finishedAt
        );
        $response = new RequestCurrentWeatherResponse($report, $weatherHotpoints);
        $this->presenter->write($response);
    }

    public function readResponse(): mixed
    {
        return $this->presenter->read();
    }
}
