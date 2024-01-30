<?php

namespace Weather\Tests\Domain\Model\Weather;

use PHPUnit\Framework\TestCase;
use Weather\Domain\Model\Weather\WeatherInfoId;

class WeatherInfoIdTest extends TestCase
{
    public function testCreate(): void
    {
        $id = new WeatherInfoId();
        $this->assertIsString($id->getId());
        $this->assertTrue(str_starts_with($id->getId(), WeatherInfoId::PREFIX_NAME));
        $this->assertEquals(40, strlen($id->getId()));
    }

    public function testCreateFrom(): void
    {
        $str = "thisIsATest";

        $id = new WeatherInfoId($str);

        $this->assertEquals($str, $id->getId());
    }

    public function testEquals(): void
    {
        $id_a = new WeatherInfoId();
        $id_b = new WeatherInfoId($id_a->getId());

        $this->assertEquals($id_a, $id_b);
    }

    public function testUnequals(): void
    {
        $id_a = new WeatherInfoId();
        $id_b = new WeatherInfoId();

        $this->assertNotEquals($id_a, $id_b);
    }
}
