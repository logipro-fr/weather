<?php

namespace Weather\Test\Infrastructure\External\WeatherStack;

use Closure;
use DateInterval;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Safe\DateTimeImmutable;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Weather\Domain\Model\Exceptions\ApiException;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\WeatherInfo;
use Weather\Infrastructure\External\WeatherApiInterface;
use Weather\Infrastructure\External\WeatherStack\WeatherStackAPI;
use Weather\Tests\TestTools\FakeGenerator;

use function Safe\json_decode;

class WeatherStackAPITest extends TestCase
{
    private string $apiKey = "apiWeatherStackKey";

    private WeatherApiInterface $api;

    public function __construct(string $name)
    {
        parent::__construct($name);
        $httpClient = $this->buildWeatherStackMockHTTPClient();

        $this->api = WeatherStackAPI::create($this->apiKey, $httpClient);
    }

    public function testCreate(): void
    {
        $api = WeatherStackApi::create($this->apiKey);
        $this->assertInstanceOf(WeatherStackApi::class, $api);
        $reflector = new ReflectionClass(WeatherStackApi::class);
        $attribute = $reflector->getProperty("weatherStackApiKey");
        $this->assertEquals($this->apiKey, $attribute->getValue($api));
    }

    public function testConstruct(): void
    {
        $api = new WeatherStackApi($this->apiKey);
        $this->assertInstanceOf(WeatherStackApi::class, $api);
        $reflector = new ReflectionClass(WeatherStackApi::class);
        $attribute = $reflector->getProperty("weatherStackApiKey");
        $this->assertEquals($this->apiKey, $attribute->getValue($api));
    }

    public function testCreateNoKey(): void
    {
        $api = WeatherStackApi::create($this->apiKey);
        $this->assertInstanceOf(WeatherStackApi::class, $api);
        $reflector = new ReflectionClass(WeatherStackApi::class);
        $attribute = $reflector->getProperty("weatherStackApiKey");
        $this->assertNotNull($attribute->getValue($api));
    }

    public function testRequestOneCurrent(): void
    {
        $response = $this->api->getFromPoints([new Point(49.003, 2.537)], new DateTimeImmutable());

        $this->assertEquals(1, count($response));
        /** @var WeatherInfo $oneResponse */
        $oneResponse = $response[0];
        /** @var object{
         *      "request": object{
         *          "type": string,
         *          "query": string
         *      },
         *      "location": object{
         *          "name": string
         *      }
         *  }
         */
        $data = json_decode($oneResponse->getData());
        $request = $data->request;
        $this->assertInstanceOf(\stdClass::class, $request);
        $this->assertEquals("Villepinte", $data->location->name);
        $this->assertEquals("LatLon", $request->type);
        $this->assertEquals("Lat 49.00 and Lon 2.54", $request->query);
        $this->assertEquals(49.003, $oneResponse->getPoint()->getLatitude());
        $this->assertEquals(2.537, $oneResponse->getPoint()->getLongitude());

        $response = $this->api->getFromPoints([new Point(0, 0)], new DateTimeImmutable());
        /** @var object{"location": object{"name": string}} */
        $data = json_decode($response[0]->getData());
        $this->assertEquals("FAKE", $data->location->name);
    }

    public function testBulk3PointsCurrent(): void
    {
        $response = $this->api->getFromPoints(
            [new Point(0, 0),new Point(1, 1),new Point(2, 2)],
            new DateTimeImmutable()
        );
        $this->assertEquals(3, count($response));

        foreach ($response as $info) {
            /** @var object{"request": object{"query": string}} $data */
            $data = json_decode($info->getData());
            $coords = explode(" and ", $data->request->query);
            $lat = floatval(substr($coords[0], 4));
            $lon = floatval(substr($coords[1], 4));
            $this->assertEquals(new Point($lat, $lon), $info->getPoint());
        }
    }

    public function testALotOfPointsCurrent(): void
    {
        $points = [];
        $total = 2000;
        $range = range(0, 90, 90 / ($total - 1));
        foreach ($range as $i) {
            array_push($points, new Point($i, $i * 2));
        }
        $response = $this->api->getFromPoints($points, new DateTimeImmutable());
        $this->assertEquals($total, count($response));

        foreach ($response as $info) {
            /** @var object{"request": object{"query": string}} $data */
            $data = json_decode($info->getData());
            $coords = explode(" and ", $data->request->query);
            $lat = floatval(substr($coords[0], 4));
            $lon = floatval(substr($coords[1], 4));
            $this->assertEquals($lat, round($info->getPoint()->getLatitude(), 2));
            $this->assertEquals($lon, round($info->getPoint()->getLongitude(), 2));
        }
    }

    public function testRequestHistorical(): void
    {
        $date = "2023-04-15";
        $response = $this->api->getFromPoints(
            [new Point(45.033, 3.883)],
            DateTimeImmutable::createFromFormat("Y-m-d", $date)
        );

        /** @var object{"current": object, "historical": object{"2023-04-15": object}} $responseData*/
        $responseData = json_decode($response[0]->getData());

        /**
         * @var object{
         *      "observation_time": string,
         *      "temperature": string,
         *      "weather_code": string,
         *      "weather_icons": array<string>,
         *      "weather_descriptions": array<string>,
         *      "wind_speed": string,
         *      "wind_degree": string,
         *      "wind_dir": string,
         *      "pressure": string,
         *      "precip": string,
         *      "humidity": string,
         *      "cloudcover": string,
         *      "feelslike": string,
         *      "uv_index": string,
         *      "visibility": string,
         *      "is_day": string
         * } $currentWeather
         */
        $currentWeather = $responseData->current;

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

        /**
         * @var object{
         *      "date": string,
         *      "date_epoch": int,
         *      "astro": object{
         *          "sunrise": string,
         *          "sunset": string,
         *          "moonrise": string,
         *          "moonset": string,
         *          "moon_phase": string,
         *          "moon_illumination": string
         *      },
         *      "mintemp": string,
         *      "maxtemp": string,
         *      "avgtemp": string,
         *      "totalsnow": string,
         *      "sunhour": string,
         *      "uv_index": string
         * } $historical
         */
        $historical = $responseData->historical->$date;

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

    public function testGetName(): void
    {
        $this->assertEquals("WeatherStack", $this->api->getName());
    }

    /**
     * @return HttpClientInterface
     */
    protected function buildWeatherStackMockHTTPClient(): HttpClientInterface
    {
        $fake = new FakeGenerator();
        return $fake->getMockHttpClient();
    }

    public function testHistoricalafterXTime(): void
    {
        $now = new DateTimeImmutable();
        $current = $now->sub(DateInterval::createFromDateString("900 seconds"));
        $history = $now->sub(DateInterval::createFromDateString("901 seconds"));
        $responseCurrent = $this->api->getFromPoints([new Point(45.033, 3.883)], $current);
        $responseHistory = $this->api->getFromPoints([new Point(45.033, 3.883)], $history);

        $this->assertFalse($responseCurrent[0]->isHistorical());
        $this->assertTrue($responseHistory[0]->isHistorical());
    }

    public function testExternalError(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(502);

        $closure = Closure::fromCallable(function () {
            return new MockResponse('{' .
                '"success": false,' .
                '"error": {' .
                    '"code": 101,' .
                    '"type": "invalid_access_key",' .
                    '"info": "You have not supplied a valid API Access Key. ' .
                    '[Technical Support: support@apilayer.com]"' .
                '}' .
            '}');
        });

        $client = new MockHttpClient($closure);

        $api = new WeatherStackAPI("null", $client);
        $api->getFromPoints([new Point(0, 0)], new DateTimeImmutable());
    }

    public function testExternalErrorHistorical(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(502);

        $closure = Closure::fromCallable(function () {
            return new MockResponse('{' .
                '"success": false,' .
                '"error": {' .
                    '"code": 418,' .
                    '"type": "I_m_a_teapot",' .
                    '"info": "I\'m a teapot and therefore cannot brew coffee"' .
                '}' .
            '}');
        });

        $client = new MockHttpClient($closure);

        $api = new WeatherStackAPI("null", $client);
        $api->getFromPoints([new Point(0, 0)], DateTimeImmutable::createFromFormat("Y", "2020"));
    }
}
