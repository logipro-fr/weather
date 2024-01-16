<?php

namespace Weather\WeatherStack\Infrastructure\Command\Tools;

class CsvParser
{
    public const DEFAULT_COLUMN_SEPARATOR = ";";
    public const DEFAULT_EOL = "\n";
    /**
     * @return array<int,array<string,string>>
     */
    public static function parse(string $csv): array
    {
        /** @var array<int,string> */
        $stringLines = str_getcsv($csv, self::DEFAULT_EOL);
        /** @var array<int,string> $lines */
        $lines = [];
        foreach ($stringLines as $line) {
            $line = strval($line);
            $lines[] = str_getcsv($line, self::DEFAULT_COLUMN_SEPARATOR);
        }
        /** @var array<int,array<int,string>> $lines */
        if (isset($lines[0])) {
            $firstLine = $lines[0];

            $callback = function (&$line) use ($firstLine) {
                $line = array_combine($firstLine, $line);
            };

            array_walk($lines, $callback);

            array_shift($lines);
        }
        /** @var array<int,array<string,string>> $lines */
        return $lines;
    }

    /**
     * garde les $length lignes d'une chaine csv en partant de la ligne $offset
     * un fichier csv commence par une ligne de nom de colonne qui n'est pas décomptée
     * @param string $csv
     * @param int $length nombre de ligne
     * @param int $offset
     * @return string
     */
    public static function trunquate(string $csv, int $length, int $offset = 0): string
    {
        if ($length < 0 || $offset < 0) {
            return $csv;
        }
        /** @var array<int,string> */
        $stringLines = str_getcsv($csv, "\n");
        $result = [];
        if (isset($stringLines[0])) {
            $firstLine = $stringLines[0];
            $result = array_merge([$firstLine], array_slice($stringLines, 1 + $offset, $length));
        }
        return implode("\n", $result);
    }
}
