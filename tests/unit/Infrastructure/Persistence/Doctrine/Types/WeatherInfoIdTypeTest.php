<?php

namespace Weather\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Weather\Domain\Model\Weather\WeatherInfoId;
use Weather\Infrastructure\Persistence\Doctrine\Types\WeatherInfoIdType;

class WeatherInfoIdTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals("weather_info_id", (new WeatherInfoIdType())->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $id = new WeatherInfoId();
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new WeatherInfoIdType())->convertToDatabaseValue($id, $platform);

        $this->assertIsString($result);
        $this->assertEquals($id->getId(), $result);
    }

    public function testConvertToPHPValue(): void
    {
        $id = "test_id_6514ab5641d165c65ef6";
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new WeatherInfoIdType())->convertToPHPValue($id, $platform);

        $this->assertInstanceOf(WeatherInfoId::class, $result);
        $this->assertEquals(new WeatherInfoid($id), $result);
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $result = (new WeatherInfoIdType())->getSQLDeclaration([], $platform);

        $this->assertEquals(Types::STRING, $result);
    }
}
