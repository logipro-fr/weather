<?php

namespace Weather\Application\GetWeather;

use Weather\APIs\WeatherApiInterface;
use Weather\Application\Presenter\PresenterInterface;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class GetWeather
{
    public function __construct(
        private PresenterInterface $presenter,
        private WeatherApiInterface $api,
        private WeatherInfoRepositoryInterface $repository
    ) {
    }

    public function execute(GetWeatherRequest $request): void
    {
        $infoArray = $this->api->getFromPoints($request->getRequestedPoints(), $request->getRequestedDate());
        $this->savePoints($infoArray);
    }

    /**
     * @param array<WeatherInfo> $infoArray
     */
    private function savePoints(array $infoArray): void
    {
        $weatherInfoDataArray = [];
        foreach ($infoArray as $weatherInfo) {
            $this->repository->save($weatherInfo);
            array_push($weatherInfoDataArray, $weatherInfo->getData());
        }
        $this->presenter->write(new GetWeatherResponse("[" . implode(",", $weatherInfoDataArray) . "]"));
    }
}
