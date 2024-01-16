<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\HistoricalWeatherApi;
use Weather\WeatherStack\Infrastructure\Command\Tools\SearchHistoricalWeatherDecorated;
use Weather\WeatherStack\Infrastructure\Command\Tools\SearchHistoricalWeatherDecoratedFactory;
use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;
use PHPUnit\Framework\TestCase;

class SearchHistoricalWeatherDecoratedFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new SearchHistoricalWeatherDecoratedFactory();
        $service = $factory->create(
            $this->createMock(HistoricalWeatherApi::class),
            new HistoricalDayRepositoryInMemory(),
        );

        $this->assertInstanceOf(SearchHistoricalWeatherDecorated::class, $service);
    }
}
