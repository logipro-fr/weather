<?php

namespace Weather\WeatherStack\Infrastructure\Command\Tools;

use Weather\Application\Share\PresenterInterface;
use Weather\Application\Share\PresenterObject;
use Weather\WeatherStack\Domain\Model\HistoricalDayRepositoryInterface;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherFactory;
use Weather\WeatherStack\HistoricalWeatherApi;

class SearchHistoricalWeatherDecoratedFactory extends SearchHistoricalWeatherFactory
{
    public function create(
        HistoricalWeatherApi $api,
        HistoricalDayRepositoryInterface $repository,
        PresenterInterface $presenter = new PresenterObject(),
    ): SearchHistoricalWeatherDecorated {
        return new SearchHistoricalWeatherDecorated($api, $repository, $presenter);
    }
}
