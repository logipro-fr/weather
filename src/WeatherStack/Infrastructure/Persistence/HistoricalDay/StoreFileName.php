<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\WeatherStack\Domain\Model\HistoricalDayId;

class StoreFileName
{
    private const STORAGE_FILE_PREFIX = "documents/weatherstack";

    private const FILENAME_PREFIX = 'P';

    public static function getFileName(HistoricalDayId $id): string
    {
        return self::FILENAME_PREFIX . strval($id->getPoint()->getLatitude()) . "," .
        strval($id->getPoint()->getLongitude());
    }

    public static function getPath(HistoricalDayId $id): string
    {
        return self::getRoot() . $id->getHistoricalDate()->format("/Y/m/d");
    }

    public static function getRoot(): string
    {
        $root = getenv("DOC_ROOT_DIR") ?: "";
        return $root . "/" . StoreFileName::STORAGE_FILE_PREFIX;
    }

    public static function getFullFileName(HistoricalDayId $id): string
    {
        return self::getPath($id) . "/" . self::getFileName($id);
    }
}
