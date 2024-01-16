<?php

namespace Weather\Tests\WeatherStack;

use Weather\Infrastructure\PredictiveModel\PredictiveModelTools;
use Weather\WeatherStack\WeatherStackTools;
use PHPUnit\Framework\TestCase;

class WeatherStackToolsTest extends TestCase
{
    public function testConstructMinimalSubsetEntry2points(): void
    {
        $GPSPoints = ["48.867,2.322","48.863,2.313"];
        /** @var array<string,\stdClass> */
        $weatherStackPoints = json_decode(
            (string)file_get_contents((__DIR__ . "/TestTools/resources/query/48.867,2.322;48.863,2.313.json"))
        );
        $GPSWithTheirWeatherStations = WeatherStackTools::constructMapCachePointsToStations(
            $GPSPoints,
            $weatherStackPoints
        );

        $this->assertEquals(2, count($GPSWithTheirWeatherStations));

        $theStation = reset($GPSWithTheirWeatherStations);
        $this->assertEquals("48.867,2.322", key($GPSWithTheirWeatherStations));
        $this->assertEquals("48.883,2.267", $theStation);

        $theStation = next($GPSWithTheirWeatherStations);
        $this->assertEquals("48.863,2.313", key($GPSWithTheirWeatherStations));
        $this->assertEquals("48.883,2.267", $theStation);
    }

    public function testConstructMinimalSubsetEntry2500Points(): void
    {
        $GPSPoints = explode(";", PredictiveModelTools::convertJsonHotPoints2String(
            (string)file_get_contents(
                __DIR__ . '/../../Infrastructure/PredictiveModel/resources/list-of-2500-hotpoints.json'
            )
        ));
        /** @var array<string,\stdClass> */
        $weatherStackPoints = json_decode(
            (string)file_get_contents((__DIR__ . "/TestTools/resources/query/bulk-2500.json"))
        );
        $GPSWithTheirWeatherStations = WeatherStackTools::constructMapCachePointsToStations(
            $GPSPoints,
            $weatherStackPoints
        );

        $this->assertEquals(2500, count($GPSWithTheirWeatherStations));

        $this->assertEquals("48.867,2.333", $GPSWithTheirWeatherStations['48.867,2.322']);
    }

    public function testConstructMapCachePointsToStations(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("arrays GPS count(1) and weatherstack count(2) must have the same size.");
        $GPSPoints = ["48.867,2.322"];
        /** @var array<string,\stdClass> */
        $weatherStackPoints = json_decode(
            (string)file_get_contents((__DIR__ . "/TestTools/resources/query/48.867,2.322;48.863,2.313.json"))
        );
         WeatherStackTools::constructMapCachePointsToStations(
             $GPSPoints,
             $weatherStackPoints
         );
    }

    public function testGet2500Hotpoint(): void
    {
        $hotpoints = WeatherStackTools::get2500Hotpoint();

        $this->assertEquals(2500, count($hotpoints));
    }
}
