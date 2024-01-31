<?php

namespace Weather\Infrastructure\Tools;

class SplitQuery
{
    private const MAX_QUERY_LENGTH = 1800;

    public function __construct(private int $maxQueryLength = self::MAX_QUERY_LENGTH)
    {
    }

    /**
     * @return array<string>
     */
    public function split(string $query): array
    {
        $queries = [];
        $parts = explode(';', $query);
        $currentQuery = '';

        foreach ($parts as $part) {
            $queryLengthWithPart = strlen($currentQuery) + strlen($part);

            if ($queryLengthWithPart > $this->maxQueryLength) {
                $queries = $this->addQueryIfNotEmpty($queries, $currentQuery);
                $currentQuery = "";
            }
            $currentQuery .= $part . ';';
        }

        $queries = $this->addQueryIfNotEmpty($queries, $currentQuery);

        return $queries;
    }

    /**
     * @param array<string> $queries
     * @return array<string>
     */
    private function addQueryIfNotEmpty(array $queries, string $query): array
    {
        $query = rtrim($query, ';');

        if (!empty($query)) {
            $queries[] = $query;
        }

        return $queries;
    }
}
