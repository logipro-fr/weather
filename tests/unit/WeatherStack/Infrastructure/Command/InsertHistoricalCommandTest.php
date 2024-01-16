<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command;

use Weather\WeatherStack\Infrastructure\Command\InsertHistoricalCommand;
use Weather\WeatherStack\Infrastructure\Command\Tools\OutputCsvPresenter;
use Weather\WeatherStack\Infrastructure\Command\Tools\SearchHistoricalWeatherDecorated;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherFactory;
use Weather\Tests\WeatherStack\TestTools\FakeHistorical;
use Weather\WeatherStack\HistoricalWeatherApi;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InsertHistoricalCommandTest extends TestCase
{
    protected function setUp(): void
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('VFSDir'));
    }

    public function testExecuteDefaultValue(): void
    {
        $fake = new FakeHistorical();
        $httpClient = $fake->getMockHttpClient();

        $api = HistoricalWeatherApi::create("pwd123", $httpClient);

        $response = new \stdClass();
        $response->csvString = "csvString";
        $response->apiQueryCount = 1;
        $response->storedQueryCount = 0;
        $presenter = $this->createMock(OutputCsvPresenter::class);
        $presenter->method('read')->willReturn($response);
        $service = $this->createMock(SearchHistoricalWeatherDecorated::class);
        $service->method('getPresenter')->willReturn($presenter);
        $factory = $this->createMock(SearchHistoricalWeatherFactory::class);
        $factory->method('create')->willReturn($service);
        $command = new InsertHistoricalCommand("testName", $api, "vfs://VFSDir", $factory);
        $commandTester = new CommandTester($command);
        $filename = __DIR__ . '/Tools/resources/accident10lignes.csv';
        $commandTester->execute(
            [
                InsertHistoricalCommand::CSV_FILENAME_ARG_NAME => $filename,
                //"outputFilename" => $outputFileName,
                //"times" => "t0,m3"
            ]
        );

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Inserting historical weather data...', $output);
        $this->assertStringContainsString('Recording file : vfs://VFSDir/result.csv', $output);
        $this->assertStringContainsString('Computing 10 lines', $output);
    }
}
