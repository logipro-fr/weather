<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\SearchHistoricalWeather;

use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeather;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherFactory;
use Weather\WeatherStack\HistoricalWeatherApi;
use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;
use PHPUnit\Framework\TestCase;

class SearchHistoricalWeatherFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new SearchHistoricalWeatherFactory();
        $service = $factory->create(
            $this->createMock(HistoricalWeatherApi::class),
            new HistoricalDayRepositoryInMemory(),
        );

        $this->assertInstanceOf(SearchHistoricalWeather::class, $service);
    }
}
