<?php

namespace Weather\Tests\WeatherStack\TestTools;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;

class FakeGeneratorTest extends TestCase
{
    private MockHttpClient $mockHttpClient;

    protected function setUp(): void
    {
        $fake = new FakeGenerator();
        $this->mockHttpClient = $fake->getMockHttpClient();
    }

    public function testGetMockHttpClientOneHotPoint(): void
    {
        $response = $this->mockHttpClient->request(
            'GET',
            'https://api.weatherstack.com/current?access_key=FAKE_KEY&query=New York&units=m',
            []
        );
        /** @var \stdClass */
        $content = json_decode($response->getContent());
        $this->assertEquals("City", $content->request->type);
        $this->assertFalse(isset($content->request->gps));

        $response = $this->mockHttpClient->request(
            'GET',
            'https://api.weatherstack.com/current?access_key=FAKE_KEY&query=49.003,2.537&units=m',
            []
        );
        /** @var \stdClass */
        $content = json_decode($response->getContent());
        $this->assertEquals("LatLon", $content->request->type);
        $this->assertEquals("Villepinte", $content->location->name);
        $this->assertEquals(48.967, $content->location->lat);
        $this->assertEquals(2.533, $content->location->lon);
        $this->assertFalse(isset($content->request->gps));

        $response = $this->mockHttpClient->request(
            'GET',
            'https://api.weatherstack.com/current?access_key=FAKE_KEY&query=1,1&units=m',
            []
        );
        /** @var \stdClass */
        $content = json_decode($response->getContent());
        $this->assertEquals("LatLon", $content->request->type);
        $this->assertEquals("FAKE", $content->location->name);
    }

    public function testGetMockHttplientOneStation(): void
    {
        $response = $this->mockHttpClient->request(
            'GET',
            'https://api.weatherstack.com/current?access_key=FAKE_KEY&query=48.967,2.533&units=m',
            []
        );
        /** @var \stdClass */
        $content = json_decode($response->getContent());
        $this->assertEquals("LatLon", $content->request->type);
        $this->assertEquals("Lat 48.97 and Lon 2.53", $content->request->query);
        $this->assertEquals("Villepinte", $content->location->name);
        $this->assertEquals(48.967, $content->location->lat);
        $this->assertEquals(2.533, $content->location->lon);
        $this->assertFalse(isset($content->request->gps));

        $response = $this->mockHttpClient->request(
            'GET',
            'https://api.weatherstack.com/current?access_key=FAKE_KEY&query=48.817,2.417&units=m',
            []
        );
        /** @var \stdClass */
        $content = json_decode($response->getContent());
        $this->assertEquals("LatLon", $content->request->type);
        $this->assertEquals("Lat 48.82 and Lon 2.42", $content->request->query);
        $this->assertEquals("Alfortville", $content->location->name);
    }

    public function testGetMockHttpClientThreeHotPoints(): void
    {
        $response = $this->mockHttpClient->request(
            'GET',
            'https://api.weatherstack.com/current?access_key=FAKE_KEY&query=49.003,2.537;43.121,5.953;0,0&units=m',
            []
        );
        /** @var array<int,\stdClass> */
        $content = json_decode($response->getContent());
        $this->assertEquals(3, count($content));
        $this->assertEquals("LatLon", $content[0]->request->type);
        $this->assertFalse(isset($content[0]->request->gps));
        $this->assertFalse(isset($content[1]->request->gps));
        $this->assertFalse(isset($content[2]->request->gps));

        $this->assertEquals("Villepinte", $content[0]->location->name);
        $this->assertEquals("Toulon", $content[1]->location->name);
        $this->assertEquals("FAKE", $content[2]->location->name);
    }
}
