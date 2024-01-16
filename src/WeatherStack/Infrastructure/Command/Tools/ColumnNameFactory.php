<?php

namespace Weather\WeatherStack\Infrastructure\Command\Tools;

class ColumnNameFactory
{
    /**
     * @return array<string,string>
     */
    public static function create(string $hourPrefix = "t0_", string $wsPrefix = "ws_"): array
    {
        return [
            "moon_illumination" => $wsPrefix . "moon_illumination",
            "totalsnow" => $wsPrefix . "totalsnow",
            "sunhour" => $wsPrefix . "sunhour",
            "temperature" => $wsPrefix . $hourPrefix . "temperature",
            "wind_speed" => $wsPrefix . $hourPrefix . "wind_speed",
            "weather_code" => $wsPrefix . $hourPrefix . "weather_code",
            "precip" => $wsPrefix . $hourPrefix . "precip",
            "humidity" => $wsPrefix . $hourPrefix . "humidity",
            "visibility" => $wsPrefix . $hourPrefix . "visibility",
            "pressure" => $wsPrefix . $hourPrefix . "pressure",
            "cloudcover" => $wsPrefix . $hourPrefix . "cloudcover",
            "heatindex" => $wsPrefix . $hourPrefix . "heatindex",
            "dewpoint" => $wsPrefix . $hourPrefix . "dewpoint",
            "windchill" => $wsPrefix . $hourPrefix . "windchill",
            "windgust" => $wsPrefix . $hourPrefix . "windgust",
            "feelslike" => $wsPrefix . $hourPrefix . "feelslike",
            "uv_index" => $wsPrefix . $hourPrefix . "uv_index",
            "sunon" => $wsPrefix . $hourPrefix . "sunon",
            "moonon" => $wsPrefix . $hourPrefix . "moonon",
        ];
    }
}
