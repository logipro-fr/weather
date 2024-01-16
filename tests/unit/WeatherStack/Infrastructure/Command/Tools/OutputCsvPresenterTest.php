<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Tools;

use Weather\Application\Share\Response;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherResponse;
use Weather\Tests\WeatherStack\Domain\Model\HistoricalHourBuilder;
use Weather\WeatherStack\Infrastructure\Command\Tools\ColumnNameFactory;
use Weather\WeatherStack\Infrastructure\Command\Tools\CsvParser;
use Weather\WeatherStack\Infrastructure\Command\Tools\Exceptions\BadClassException;
use Weather\WeatherStack\Infrastructure\Command\Tools\Exceptions\NotSameLineNumberException;
use Weather\WeatherStack\Infrastructure\Command\Tools\OutputCsvPresenter;
use PHPUnit\Framework\TestCase;

use function Safe\file_get_contents;

class OutputCsvPresenterTest extends TestCase
{
    public function testColumnName(): void
    {
        $presenter = $this->makeOutputCsvPresenter();

        $response = new SearchHistoricalWeatherResponse(
            [
                0 => [
                    HistoricalHourBuilder::aHistoricalHour()->withHour(10)->build()
                ],
                -3 => [
                    HistoricalHourBuilder::aHistoricalHour()->withHour(7)->build()
                ],
            ],
            2,
            3,
            []
        );

        $presenter->write($response);

        $csvResult = $presenter->read();

        $this->assertEquals(2, $csvResult->apiQueryCount);
        $this->assertEquals(3, $csvResult->storedQueryCount);

        $expectedColNames = $this->getExpectedColNames(
            [
                'ws_t0_moon_illumination',
                'ws_t0_totalsnow',
                'ws_t0_sunhour',
                'ws_t0_temperature','ws_t0_wind_speed','ws_t0_weather_code','ws_t0_precip','ws_t0_humidity',
                'ws_t0_visibility','ws_t0_pressure','ws_t0_cloudcover','ws_t0_heatindex','ws_t0_dewpoint',
                'ws_t0_windchill','ws_t0_windgust','ws_t0_feelslike','ws_t0_uv_index','ws_t0_sunon','ws_t0_moonon',
                'ws_m3_moon_illumination',
                'ws_m3_totalsnow',
                'ws_m3_sunhour',
                'ws_m3_temperature','ws_m3_wind_speed','ws_m3_weather_code','ws_m3_precip','ws_m3_humidity',
                'ws_m3_visibility','ws_m3_pressure','ws_m3_cloudcover','ws_m3_heatindex','ws_m3_dewpoint',
                'ws_m3_windchill','ws_m3_windgust','ws_m3_feelslike','ws_m3_uv_index','ws_m3_sunon','ws_m3_moonon',
            ]
        );
        $lines = CsvParser::parse($csvResult->csvString);
        $this->assertEquals(1, count($lines));
        $this->assertEquals($expectedColNames, array_keys($lines[0]));
    }

    private function makeOutputCsvPresenter(): OutputCsvPresenter
    {
        $csv = file_get_contents(__DIR__ . '/resources/accident1ligne.csv');
        $columns = ColumnNameFactory::create("", "");
        return new OutputCsvPresenter(
            $csv,
            $columns
        );
    }

    /**
     * @param array<string> $additionalCols
     * @return array<string>
     */
    private function getExpectedColNames(array $additionalCols): array
    {
        $expectedColNames = ["","Num_Acc","jour","mois","an","hrmn",
            "lum","agg","int","atm","col","lat","long","hour","minute","severity"];

        $additionalNames = array_values($additionalCols);
        return array_merge($expectedColNames, $additionalNames);
    }

    public function testBadClassException(): void
    {
        $this->expectException(BadClassException::class);
        $this->expectExceptionMessageMatches(
            "/'.*' class passed. Only SearchHistoricalWeatherResponse class allowed./"
        );
        $csv = file_get_contents(__DIR__ . '/resources/accident1ligne.csv');
        $columns = ColumnNameFactory::create();
        $presenter = new OutputCsvPresenter(
            $csv,
            $columns
        );
        $response = $this->createMock(Response::class);
        $presenter->write($response);
    }

    public function testNotSameLineNumberException(): void
    {
        $this->expectException(NotSameLineNumberException::class);
        $this->expectExceptionMessage(
            "Number of lines must be the same between csv file (1 lines) and query (2 lines)"
        );
        $presenter = $this->makeOutputCsvPresenter();

        $response = new SearchHistoricalWeatherResponse([
            0 => [
                HistoricalHourBuilder::aHistoricalHour()->withHour(10)->build(),
                HistoricalHourBuilder::aHistoricalHour()->withHour(7)->build()
            ],
        ], 0, 0, []);

        $presenter->write($response);
        $presenter->read();
    }
}
