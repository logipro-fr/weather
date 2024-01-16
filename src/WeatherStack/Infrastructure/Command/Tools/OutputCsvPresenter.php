<?php

namespace Weather\WeatherStack\Infrastructure\Command\Tools;

use Weather\Application\Share\PresenterInterface;
use Weather\Application\Share\Response;
use Weather\WeatherStack\Domain\Model\HistoricalHour;
use Weather\WeatherStack\Application\Service\SearchHistoricalWeather\SearchHistoricalWeatherResponse;
use Weather\WeatherStack\Infrastructure\Command\Tools\Exceptions\BadClassException;
use Weather\WeatherStack\Infrastructure\Command\Tools\Exceptions\NotSameLineNumberException;

class OutputCsvPresenter implements PresenterInterface
{
    private SearchHistoricalWeatherResponse $response;

    /**
     * @param array<string,string> $columnsNeededNames
     * @return void
     */
    public function __construct(
        private string $initialCsv,
        private array $columnsNeededNames
    ) {
    }

    public function write(Response $response): void
    {
        $this->response = $this->getSearchHistoricalWeatherResponse($response);
    }

    private function getSearchHistoricalWeatherResponse(Response $response): SearchHistoricalWeatherResponse
    {
        if (!($response instanceof SearchHistoricalWeatherResponse)) {
            throw new BadClassException(
                sprintf(
                    "'%s' class passed. Only SearchHistoricalWeatherResponse class allowed.",
                    $response::class
                )
            );
        };
        return $response;
    }

    /**
     * @return \stdClass
     */
    public function read(): \stdClass
    {
        $objectsLines = $this->extractLines($this->initialCsv);
        $lines = $this->addColumnsOfHistoricalHours($objectsLines);
        $csvString = $this->linesToCsv($lines);

        $result = new \stdClass();
        $result->csvString = $csvString;
        $result->apiQueryCount = $this->response->apiQueryCount;
        $result->storedQueryCount = $this->response->storedQueryCount;
        return $result;
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function extractLines(string $csv): array
    {
        $lines = CsvParser::parse($csv);

        return $lines;
    }

    /**
     * @param array<int,array<string,mixed>> $lines
     * @return array<int,array<string,mixed>>
     */
    private function addColumnsOfHistoricalHours(array $lines): array
    {

        $allHours = $this->response->wishedHourArrays;

        $nbLine = count($lines);

        $prefixes = [];
        foreach ($allHours as $hour => $historicals) {
            $prefix = $hour < 0 ? sprintf("ws_m%s_", -$hour) : sprintf("ws_t%s_", $hour);
            $prefixes[$hour] = $prefix;
            if (count($historicals) != $nbLine) {
                throw new NotSameLineNumberException(
                    sprintf(
                        "Number of lines must be the same between csv file (%s lines) and query (%s lines)",
                        count($lines),
                        count($this->response->wishedHourArrays[$hour])
                    )
                );
            }
        }

        $index = 0;
        foreach ($lines as $lineToComplete) {
            foreach ($allHours as $hour => $aHourInDay) {
                $lineToComplete = $this->completeLineWithHistoricalHour(
                    $lineToComplete,
                    $aHourInDay[$index],
                    $prefixes[$hour]
                );
            }
            $lines[$index] = $lineToComplete;
            $index++;
        }
        return $lines;
    }

    /**
     * @param array<string,mixed> $line
     * @return array<string,mixed>
     */
    private function completeLineWithHistoricalHour(array $line, HistoricalHour $hour, string $prefix): array
    {
        foreach ($this->columnsNeededNames as $varName => $colName) {
            $value = $hour->get($varName);
            $colName = $prefix . $colName;
            $line[$colName] = $value;
        }
        return $line;
    }

    /**
     * @param array<int,array<string,mixed>> $lines
     */
    private function linesToCsv(array $lines): string
    {
        // Écrire les en-têtes de colonnes
        $entetes = array_keys($lines[0]);
        $csvString = implode(';', $entetes) . "\n";

        // Écrire les données
        foreach ($lines as $ligne) {
            $csvString .= implode(';', $ligne) . "\n";
        }
        return $csvString;
    }
}
