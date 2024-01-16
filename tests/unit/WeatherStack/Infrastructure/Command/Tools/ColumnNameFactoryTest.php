<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\Infrastructure\Command\Tools\ColumnNameFactory;
use PHPUnit\Framework\TestCase;

class ColumnNameFactoryTest extends TestCase
{
    public function testSimple(): void
    {
        $colNames = ColumnNameFactory::create("", "");

        $expectedColNames = [
            "moon_illumination" => "moon_illumination",
            "totalsnow" => "totalsnow",
            "sunhour" => "sunhour",
            "temperature" => "temperature",
            "wind_speed" => "wind_speed",
            "weather_code" => "weather_code",
            "precip" => "precip",
            "humidity" => "humidity",
            "visibility" => "visibility",
            "pressure" => "pressure",
            "cloudcover" => "cloudcover",
            "heatindex" => "heatindex",
            "dewpoint" => "dewpoint",
            "windchill" => "windchill",
            "windgust" => "windgust",
            "feelslike" => "feelslike",
            "uv_index" => "uv_index",
            "sunon" => "sunon",
            "moonon" => "moonon",
        ];

        $this->assertEquals($expectedColNames, $colNames);
    }

    public function testCreateWithPrefix(): void
    {
        $colNames = ColumnNameFactory::create("t0_", "");

        $expectedColNames = [
            "moon_illumination" => "moon_illumination",
            "totalsnow" => "totalsnow",
            "sunhour" => "sunhour",
            "temperature" => "t0_temperature",
            "wind_speed" => "t0_wind_speed",
            "weather_code" => "t0_weather_code",
            "precip" => "t0_precip",
            "humidity" => "t0_humidity",
            "visibility" => "t0_visibility",
            "pressure" => "t0_pressure",
            "cloudcover" => "t0_cloudcover",
            "heatindex" => "t0_heatindex",
            "dewpoint" => "t0_dewpoint",
            "windchill" => "t0_windchill",
            "windgust" => "t0_windgust",
            "feelslike" => "t0_feelslike",
            "uv_index" => "t0_uv_index",
            "sunon" => "t0_sunon",
            "moonon" => "t0_moonon",
        ];

        $this->assertEquals($expectedColNames, $colNames);
    }

    public function testCreateWithWsPrefix(): void
    {
        $colNames = ColumnNameFactory::create();

        $expectedColNames = [
            "moon_illumination" => "ws_moon_illumination",
            "totalsnow" => "ws_totalsnow",
            "sunhour" => "ws_sunhour",
            "temperature" => "ws_t0_temperature",
            "wind_speed" => "ws_t0_wind_speed",
            "weather_code" => "ws_t0_weather_code",
            "precip" => "ws_t0_precip",
            "humidity" => "ws_t0_humidity",
            "visibility" => "ws_t0_visibility",
            "pressure" => "ws_t0_pressure",
            "cloudcover" => "ws_t0_cloudcover",
            "heatindex" => "ws_t0_heatindex",
            "dewpoint" => "ws_t0_dewpoint",
            "windchill" => "ws_t0_windchill",
            "windgust" => "ws_t0_windgust",
            "feelslike" => "ws_t0_feelslike",
            "uv_index" => "ws_t0_uv_index",
            "sunon" => "ws_t0_sunon",
            "moonon" => "ws_t0_moonon",
        ];

        $this->assertEquals($expectedColNames, $colNames);
    }
}
