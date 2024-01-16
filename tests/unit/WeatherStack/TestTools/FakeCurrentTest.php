<?php

namespace Weather\Tests\WeatherStack\TestTools;

use PHPUnit\Framework\TestCase;

use function Safe\json_decode;

class FakeCurrentTest extends TestCase
{
    public function testOnePoint(): void
    {
        $fake = new FakeCurrent();

        $url = "https://api.weatherstack.com/current?access_key=1234&" .
            "query=49.003,2.537";

        $response = $fake->getMockHttpClient()->request("GET", $url);

        $this->assertJson($response->getContent());
        $this->assertEquals(200, $response->getStatusCode());

        /** @var \stdClass */
        $point = json_decode($response->getContent());

        $this->assertEquals("LatLon", $point->request->type);
        $this->assertEquals("Lat 49.00 and Lon 2.54", $point->request->query);
        $this->assertEquals("en", $point->request->language);
        $this->assertEquals("m", $point->request->unit);

        $this->assertEquals("Villepinte", $point->location->name);
        $this->assertEquals("France", $point->location->country);
        $this->assertEquals("Ile-de-France", $point->location->region);
        $this->assertEquals("48.967", $point->location->lat);
        $this->assertEquals("2.533", $point->location->lon);
        $this->assertEquals("Europe/Paris", $point->location->timezone_id);
        $this->assertEquals("2023-06-21 16:14", $point->location->localtime);
        $this->assertEquals(1687364040, $point->location->localtime_epoch);
        $this->assertEquals("2.0", $point->location->utc_offset);

        $this->assertEquals("02:14 PM", $point->current->observation_time);
        $this->assertEquals(26, $point->current->temperature);
        $this->assertEquals(116, $point->current->weather_code);
        $this->assertEquals(
            ["https://cdn.worldweatheronline.com/images/wsymbols01_png_64/wsymbol_0002_sunny_intervals.png"],
            $point->current->weather_icons
        );
        $this->assertEquals([ "Partly cloudy"], $point->current->weather_descriptions);
        $this->assertEquals(9, $point->current->wind_speed);
        $this->assertEquals(70, $point->current->wind_degree);
        $this->assertEquals("ENE", $point->current->wind_dir);
        $this->assertEquals(1018, $point->current->pressure);
        $this->assertEquals(0, $point->current->precip);
        $this->assertEquals(51, $point->current->humidity);
        $this->assertEquals(50, $point->current->cloudcover);
        $this->assertEquals(26, $point->current->feelslike);
        $this->assertEquals(8, $point->current->uv_index);
        $this->assertEquals(10, $point->current->visibility);
        $this->assertEquals("yes", $point->current->is_day);
    }

    public function testTwoPoint(): void
    {
        $fake = new FakeCurrent();

        $url = "https://api.weatherstack.com/current?access_key=1234&" .
            "query=49.003,2.537;48.863,2.313";

        $response = $fake->getMockHttpClient()->request("GET", $url);

        /** @var array<\stdClass> */
        $twoPoints = json_decode($response->getContent());
        $this->assertEquals(2, count($twoPoints));
        $this->assertEquals("Lat 49.00 and Lon 2.54", $twoPoints[0]->request->query);
        $this->assertEquals("Villepinte", $twoPoints[0]->location->name);
        $this->assertEquals("48.967", $twoPoints[0]->location->lat);
        $this->assertEquals("2.533", $twoPoints[0]->location->lon);
        $this->assertEquals("02:14 PM", $twoPoints[0]->current->observation_time);
        $this->assertEquals(26, $twoPoints[0]->current->temperature);

        $this->assertEquals("Lat 48.86 and Lon 2.31", $twoPoints[1]->request->query);
        $this->assertEquals("Neuilly-Sur-Seine", $twoPoints[1]->location->name);
        $this->assertEquals("48.883", $twoPoints[1]->location->lat);
        $this->assertEquals("2.267", $twoPoints[1]->location->lon);
        $this->assertEquals("02:14 PM", $twoPoints[1]->current->observation_time);
        $this->assertEquals(27, $twoPoints[1]->current->temperature);
    }

    public function testUrlFormat(): void
    {
        $fake = new FakeCurrent();

        $url = "https://api.weatherstack.com/historical?access_key=1234&" .
            "query=49.003,2.537&units=m";

        $response = $fake->getMockHttpClient()->request("GET", $url);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
