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
        $infoArray = $this->api->getFromPoints($request->getPoints(), $request->getDate());
        $this->savePoints($infoArray);
    }

    /**
     * @param array<WeatherInfo> $infoArray
     */
    private function savePoints(array $infoArray): void
    {
        $dataArray = [];
        foreach ($infoArray as $info) {
            $this->repository->add($info);
            array_push($dataArray, $info->getData());
        }
        $this->presenter->write(new GetWeatherResponse("[" . implode(",", $dataArray) . "]"));
    }
}
