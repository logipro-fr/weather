<?php

namespace Weather\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\LocationTimeDTO;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeather;
// Line is too long for codecheck, we ignore warning
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherAbstractService; // phpcs:ignore
use Symfony\Component\Console\Helper\ProgressBar;

class SearchHistoricalWeatherDecorated extends SearchHistoricalWeather implements SearchHistoricalWeatherAbstractService
{
    private ProgressBar $progressBar;

    protected function hookLoop(
        LocationTimeDTO $locationTime,
    ): void {
        $this->progressBar->advance();
    }

    public function setProgressBar(ProgressBar $progressBar): void
    {
        $this->progressBar = $progressBar;
    }
}
