<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\Infrastructure\Command\Tools\ConvertCsvToLocationTimeDTO;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\LocationTimeDTO;
use PHPUnit\Framework\TestCase;

use function Safe\file_get_contents;

class ConvertCsvToLocationTimeDTOTest extends TestCase
{
    public function testConvert(): void
    {
        $csv = file_get_contents(__DIR__ . '/resources/accident1ligne.csv');

        $dtos = ConvertCsvToLocationTimeDTO::convert($csv);

        $this->assertEquals(1, count($dtos));

        $this->assertInstanceOf(LocationTimeDTO::class, $dtos[0]);

        $this->assertEquals(new LocationTimeDTO(44.039, 4.348, "2023-01-25 7"), $dtos[0]);
    }
}
