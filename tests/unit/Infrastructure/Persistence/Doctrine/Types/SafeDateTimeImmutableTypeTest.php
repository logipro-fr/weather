<?php

namespace Weather\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Infrastructure\Persistence\Doctrine\Types\SafeDateTimeImmutableType;

class SafeDateTimeImmutableTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals("safe_datetime_immutable", (new SafeDateTimeImmutableType())->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $date = new DateTimeImmutable("2024-01-01 13:55:59.123456");
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new SafeDateTimeImmutableType())->convertToDatabaseValue($date, $platform);

        $this->assertIsString($result);
        $this->assertEquals($date, new DateTimeImmutable($result));
    }

    public function testConvertToPHPValue(): void
    {
        $date = "2024-01-01 00:00:10.654321";
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new SafeDateTimeImmutableType())->convertToPHPValue($date, $platform);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals($date, $result->format("Y-m-d H:i:s.u"));
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $result = (new SafeDateTimeImmutableType())->getSQLDeclaration([], $platform);

        $this->assertEquals(Types::DATETIME_IMMUTABLE, $result);
    }
}
