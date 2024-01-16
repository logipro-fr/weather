<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\HistoricalDayRepositoryInMemory;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\LocationTimeDTO;
use Weather\WeatherStack\HistoricalWeatherApi;
use Weather\WeatherStack\Infrastructure\Command\Tools\SearchHistoricalWeatherDecorated;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class SearchHistoricalWeatherDecoratedTest extends TestCase
{
    public function testProgressBarAdvance(): void
    {
        $api = $this->createMock(HistoricalWeatherApi::class);
        $decoratdService = new SearchHistoricalWeatherDecorated($api, new HistoricalDayRepositoryInMemory());

        $bar = new ProgressBar($this->createMock(OutputInterface::class));
        $this->assertEquals(0, $bar->getProgress());

        $decoratdService->setProgressBar($bar);

        $hookLoopMethod = new ReflectionMethod($decoratdService, 'hookLoop');
        $hookLoopMethod->setAccessible(true);

        $hookLoopMethod->invoke($decoratdService, $this->createMock(LocationTimeDTO::class));

        $this->assertEquals(1, $bar->getProgress());
    }
}
