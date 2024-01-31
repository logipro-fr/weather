<?php

namespace Weather\Application\GetWeather;

use Weather\Infrastructure\External\WeatherApiInterface;
use Weather\Application\Presenter\AbstractPresenter;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Domain\Model\Weather\WeatherInfoRepositoryInterface;

class GetWeather implements ServiceInterface
{
    public function __construct(
        private AbstractPresenter $presenter,
        private WeatherApiInterface $api,
        private WeatherInfoRepositoryInterface $repository
    ) {
    }

    /**
     * @param GetWeatherRequest $request
     */
    public function execute(RequestInterface $request): void
    {
        $infoArray = $this->api->getFromPoints($request->getRequestedPoints(), $request->getRequestedDate());
        $this->savePoints($infoArray);
    }

    /**
     * @param array<WeatherInfo> $infoArray
     */
    private function savePoints(array $infoArray): void
    {
        foreach ($infoArray as $weatherInfo) {
            $this->repository->save($weatherInfo);
        }
        $this->presenter->write(new GetWeatherResponse($infoArray));
    }

    public function getPresenter(): AbstractPresenter
    {
        return $this->presenter;
    }
}
