<?php

namespace Weather\Tests\WeatherStack;

use Weather\Infrastructure\PredictiveModel\PredictiveModelTools;
use Weather\Share\Domain\Point;
use Weather\WeatherStack\WeatherStackApi;
use Weather\Tests\WeatherStack\TestTools\FakeGenerator;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WeatherStackAPITest extends TestCase
{
    private string $apiKey = "apiWeatherStackKey";

    private WeatherStackApi $api;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $httpClient = $this->buildWeatherStackMockHTTPClient();

        $this->api = WeatherStackApi::create($this->apiKey, $httpClient);
    }

    public function testCreate(): void
    {
        $api = WeatherStackApi::create($this->apiKey);
        $this->assertInstanceOf(WeatherStackApi::class, $api);
    }

    public function testRequestCurrent(): void
    {

        $response = $this->api->request("New York");

        $this->assertEquals(1, count($response));

        $currentWeather = $response[0]->current;

        $this->assertEquals("12:14 PM", $currentWeather->observation_time);
        $this->assertEquals(13, $currentWeather->temperature);
        $this->assertEquals(113, $currentWeather->weather_code);
        $this->assertEquals(
            ["https://assets.weatherstack.com/images/wsymbols01_png_64/wsymbol_0001_sunny.png"],
            $currentWeather->weather_icons
        );
        $this->assertEquals(["Sunny"], $currentWeather->weather_descriptions);
        $this->assertEquals(0, $currentWeather->wind_speed);
        $this->assertEquals(349, $currentWeather->wind_degree);
        $this->assertEquals("N", $currentWeather->wind_dir);
        $this->assertEquals(1010, $currentWeather->pressure);
        $this->assertEquals(0, $currentWeather->precip);
        $this->assertEquals(90, $currentWeather->humidity);
        $this->assertEquals(0, $currentWeather->cloudcover);
        $this->assertEquals(13, $currentWeather->feelslike);
        $this->assertEquals(4, $currentWeather->uv_index);
        $this->assertEquals(16, $currentWeather->visibility);

        $this->assertEquals(1, $this->api->getLastRequestNumber());

        $request = $response[0]->request;

        $this->assertEquals("City", $request->type);
        $this->assertEquals("New York, United States of America", $request->query);
        $this->assertEquals("en", $request->language);
        $this->assertEquals("m", $request->unit);
        $this->assertFalse(isset($request->gps));
    }

    public function testRequestOneGPS(): void
    {
        $response = $this->api->request("49.003,2.537");

        $this->assertEquals(1, count($response));
        $oneResponse = $response[0];
        $request = $oneResponse->request;
        $this->assertInstanceOf(\stdClass::class, $request);
        $this->assertEquals("Villepinte", $oneResponse->location->name);
        $this->assertTrue(isset($request->gps));
        $this->assertEquals("LatLon", $request->type);
        $this->assertEquals("Lat 48.97 and Lon 2.53", $request->query);
        $this->assertEquals(49.003, $request->gps->on_Latitude);
        $this->assertEquals(2.537, $request->gps->on_Longitude);

        $response = $this->api->request("0,0");
        $this->assertEquals("FAKE", $response[0]->location->name);
    }

    public function testStrangeCase(): void
    {

        $response = $this->api->request("48.817,2.417");

        $this->assertEquals(48.800, $response[0]->location->lat); // c'est une station

        $this->assertEquals("Alfortville", $response[0]->location->name);
        $this->assertTrue(isset($response[0]->request->gps));
        $this->assertEquals(48.817, $response[0]->request->gps->on_Latitude);
        $this->assertEquals(2.417, $response[0]->request->gps->on_Longitude);
        $this->assertEquals(2.417, $response[0]->location->lon);
    }

    /**
     * @return HttpClientInterface
     */
    protected function buildWeatherStackMockHTTPClient(): HttpClientInterface
    {
        $fake = new FakeGenerator();
        return $fake->getMockHttpClient();
    }

    public function testBulk2Points(): void
    {
        $response = $this->api->request("0,0;1,1");
        $this->assertEquals(2, count($response));
        $this->assertEquals(2, $this->api->getLastRequestNumber());
    }

    public function testRequest5PointsAndcountLessRequest(): void
    {
        $response = $this->api->request("49.003,2.537;43.121,5.953;48.867,2.322;48.863,2.313;43.133,5.985");
        $this->assertEquals(5, count($response));
        $this->assertEquals(4, $this->api->getLastRequestNumber());
    }

    public function testBulk2500Points(): void
    {

        $point2500 = PredictiveModelTools::convertJsonHotPoints2String((string)file_get_contents(
            __DIR__ . '/resources/list-of-2500-hotpoints.json'
        ));

        $response = $this->api->request($point2500);

        $this->assertEquals(2500, count($response));
        $this->assertEquals(290, $this->api->getLastRequestNumber());
    }

    public function testRequestHistorical(): void
    {
        $response = $this->api->requestHistorical("Le Puy en Velay", "2023-04-15");

        $currentWeather = $response->current;

        $this->assertEquals("09:53 AM", $currentWeather->observation_time);
        $this->assertEquals(18, $currentWeather->temperature);
        $this->assertEquals(389, $currentWeather->weather_code);
        $this->assertEquals(
            ["https://cdn.worldweatheronline.com/images/wsymbols01_png_64/wsymbol_0024_thunderstorms.png"],
            $currentWeather->weather_icons
        );
        $this->assertEquals(["Rain, Thunderstorm In Vicinity"], $currentWeather->weather_descriptions);
        $this->assertEquals(7, $currentWeather->wind_speed);
        $this->assertEquals(200, $currentWeather->wind_degree);
        $this->assertEquals("SSW", $currentWeather->wind_dir);
        $this->assertEquals(1019, $currentWeather->pressure);
        $this->assertEquals(0.1, $currentWeather->precip);
        $this->assertEquals(88, $currentWeather->humidity);
        $this->assertEquals(75, $currentWeather->cloudcover);
        $this->assertEquals(18, $currentWeather->feelslike);
        $this->assertEquals(4, $currentWeather->uv_index);
        $this->assertEquals(10, $currentWeather->visibility);
        $this->assertEquals("yes", $currentWeather->is_day);

        $date = "2023-04-15";
        $historical = $response->historical->$date;

        $this->assertEquals("2023-04-15", $historical->date);
        $this->assertEquals(1681516800, $historical->date_epoch);
        $this->assertEquals("07:01 AM", $historical->astro->sunrise);
        $this->assertEquals("08:29 PM", $historical->astro->sunset);
        $this->assertEquals("05:09 AM", $historical->astro->moonrise);
        $this->assertEquals("02:44 PM", $historical->astro->moonset);
        $this->assertEquals("Waning Crescent", $historical->astro->moon_phase);
        $this->assertEquals(20, $historical->astro->moon_illumination);
        $this->assertEquals(6, $historical->mintemp);
        $this->assertEquals(11, $historical->maxtemp);
        $this->assertEquals(8, $historical->avgtemp);
        $this->assertEquals(0, $historical->totalsnow);
        $this->assertEquals(7.9, $historical->sunhour);
        $this->assertEquals(2, $historical->uv_index);
    }

    public function testHistorical1Point(): void
    {
        $response = $this->api->requestHistoricalBulk("2023/01/25/45.033,3.883", "2023-03-01");

        $this->assertEquals(1, count($response));
    }

    public function testHistorical2500Points(): void
    {
        $response = $this->api->requestHistoricalBulk("bulk-2500", "2023-03-01");

        $this->assertEquals(2499, count($response));
    }

    public function testMinimizeRequestQuantityBulk2Points(): void
    {
        $response = $this->api->request("48.867,2.322;48.863,2.313");
        $this->assertEquals(2, count($response));
        $requestNumber = $this->api->getLastRequestNumber();
        $this->assertEquals(1, $requestNumber);

        $this->assertEquals(48.867, $response[0]->request->gps->on_Latitude);
        $this->assertEquals(2.322, $response[0]->request->gps->on_Longitude);
        $this->assertEquals(48.863, $response[1]->request->gps->on_Latitude);
        $this->assertEquals(2.313, $response[1]->request->gps->on_Longitude);
    }

    public function testMinimizeQuery(): void
    {
        $query = "48.867,2.322;48.863,2.313";
        $this->assertEquals("48.867,2.333", WeatherStackApi::minimizeQueries($query));

        $query = "49.003,2.537;43.121,5.953;48.867,2.322;48.863,2.313";
        $this->assertEquals("48.967,2.533;43.117,5.933;48.867,2.333", WeatherStackApi::minimizeQueries($query));

        $query = PredictiveModelTools::convertJsonHotPoints2String(
            (string)file_get_contents(
                __DIR__ . '/resources/list-of-2500-hotpoints.json'
            )
        );

        $minQ = WeatherStackApi::minimizeQueries($query);
        $this->assertEquals(2500, count(explode(";", $query)));
        $this->assertEquals(281, count(explode(";", $minQ)));
    }

    public function testGetLastRequestNumber(): void
    {
        $response = $this->api->request("48.867,2.322");
        $this->assertEquals(1, count($response));

        $response = $this->api->request("48.863,2.313");
        $this->assertEquals(1, count($response));

        $response = $this->api->request("48,2;49,2.4");
        $this->assertEquals(2, count($response));

        $response = $this->api->request("48,2;49,2.4;12,-1");
        $this->assertEquals(3, count($response));
    }

    public function testGetJsonCurrentWeather(): void
    {
        $hotpoints = [
            new Point(48.867, 2.322),
            new Point(48.863, 2.313)
        ];
        $json = $this->api->getJsonCurrentWeather($hotpoints);
        $this->assertEquals(2, count($json));
    }
    public function testGetResultForPointWithBasicRequest(): void
    {
        $point = '48.863,2.313';

        $reflectionMethod = new ReflectionMethod(WeatherStackApi::class, 'getCurrentWeatherByPoint');
        $reflectionMethod->setAccessible(true);

        $cached = [];
        $stations = [];
        $result = $reflectionMethod->invoke($this->api, $point, $cached, $stations);

        $this->assertInstanceOf(\stdClass::class, $result);
    }
}
