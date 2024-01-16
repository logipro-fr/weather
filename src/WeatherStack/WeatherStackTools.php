<?php

namespace Weather\WeatherStack;

use Weather\Infrastructure\PredictiveModel\PredictiveModelTools;
use Weather\Share\Domain\Point;

class WeatherStackTools
{
    /**
     * @param array<string> $gpsPoints
     * @param array<string,\stdClass> $weatherStackPoints
     * @return array<string,string>
     */
    public static function constructMapCachePointsToStations(
        ?array $gpsPoints = null,
        ?array $weatherStackPoints = null
    ): array {
        if ($gpsPoints == null) {
            $gpsPoints = explode(";", PredictiveModelTools::convertJsonHotPoints2String(
                ($file = file_get_contents(__DIR__ . '/resources/list-of-2500-hotpoints.json')) == false ?
                    "" : $file
            ));
        }
        if ($weatherStackPoints == null) {
            /** @var array<string,\stdClass> */
            $weatherStackPoints = json_decode(
                ($file = file_get_contents(__DIR__ . "/resources/bulk-2500.json")) == false ?
                    "" : $file
            );
        }
        if (($c1 = count($gpsPoints)) != ($c2 = count($weatherStackPoints))) {
            throw new \Exception("arrays GPS count($c1) and weatherstack count($c2) must have the same size.");
        }

        /** @var array<string,string> */
        $mapPoint2WeatherStation = [];

        $point = (string) reset($gpsPoints);
        /** @var \stdClass $item */
        foreach ($weatherStackPoints as $item) {
            $key = $item->location->lat . ',' . $item->location->lon;
            $mapPoint2WeatherStation[$point] = $key;
            $point = (string) next($gpsPoints);
        }
        return $mapPoint2WeatherStation;
    }


    /**
     * @return array<Point>
     */
    public static function get2500Hotpoint(): array
    {
        $points = explode(";", PredictiveModelTools::convertJsonHotPoints2String(
            ($file = file_get_contents(__DIR__ . '/resources/list-of-2500-hotpoints.json')) == false ?
                "" : $file
        ));
        $hotpoints = [];
        foreach ($points as $point) {
            list($latitude, $longitude) = explode(",", $point);
            $hotpoints[] = new Point(floatval($latitude), floatval($longitude));
        }
        return $hotpoints;
    }
}
