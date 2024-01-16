<?php

namespace Weather\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\LocationTimeDTO;

class ConvertCsvToLocationTimeDTO
{
    /**
     * @return array<LocationTimeDTO>
     */
    public static function convert(string $csv): array
    {
        $lines = CsvParser::parse($csv);

        $locationTimes = [];
        foreach ($lines as $line) {
            $datetime =
                $line['an'] . "-" .
                $line['mois'] . "-" .
                $line['jour'] . " " .
                $line['hour'];
            $latitude = floatval($line['lat']);
            $longitude = floatval($line['long']);
            $locationTimes[] = new LocationTimeDTO(
                $latitude,
                $longitude,
                $datetime
            );
        }
        return $locationTimes;
    }
}
