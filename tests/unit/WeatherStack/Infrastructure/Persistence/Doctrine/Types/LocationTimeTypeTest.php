<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\Doctrine\Types;

use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Infrastructure\Persistence\Doctrine\Types\LocationTimeType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class LocationTimeTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals('location_time', (new LocationTimeType())->getName());
    }

    public function testConvertToPHPValue(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);

        $type = new LocationTimeType();
        $lt = $type->convertToPHPValue("45.045 3.889 2022-10-18/11:59:45", $platform);
        $this->assertInstanceOf(LocationTime::class, $lt);
        $this->assertEquals(45.045, $lt->getLatitude());
        $this->assertEquals(3.889, $lt->getLongitude());
        $this->assertEquals("2022/10/18 11:59:45", $lt->getTime()->format("Y/m/d H:i:s"));
    }

    public function testConvertToDatabaseValue(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $type = new LocationTimeType();
        $lt = new LocationTime(
            45.045,
            3.889,
            DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2022-10-18 11:59:45")
        );
        $dbValue = $type->convertToDatabaseValue($lt, $platform);
        $this->assertEquals("45.045 3.889 2022-10-18 11:59:45", $dbValue);
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = new MariaDBPlatform();
        $type = new LocationTimeType();
        $lt = new LocationTime(
            45.045,
            3.889,
            DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2022-10-18 11:59:45")
        );
        $sqlDeclaration = $type->getSQLDeclaration([], $platform);
        $this->assertEquals("CHAR(36)", $sqlDeclaration);
    }
}
