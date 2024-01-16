<?php

namespace Weather\WeatherStack\Application\Service\SearchHistoricalWeather;

use Weather\Application\Share\PresenterInterface;
use Weather\Application\Share\PresenterObject;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\HistoricalWeatherApi;

interface SearchHistoricalWeatherAbstractFactory
{
    public function create(
        HistoricalWeatherApi $api,
        HistoricalDayRepositoryInterface $repository,
        PresenterInterface $presenter = new PresenterObject(),
    ): SearchHistoricalWeatherAbstractService;
}
