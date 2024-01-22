<?php

namespace Weather\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Weather\Point;
use Weather\Infrastructure\Persistence\Doctrine\Types\SafeDateTimeImmutableType;

class SafeDateTimeImmutableTypeTest extends TestCase{
    public function testGetName(): void
    {
        $this->assertEquals("point", (new SafeDateTimeImmutableType())->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $id = new Point(3.621,69.420);
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new SafeDateTimeImmutableType())->convertToDatabaseValue($id, $platform);

        $this->assertIsString($result);
        $this->assertEquals($id->__toString(), $result);
    }

    public function testConvertToPHPValue(): void
    {
        $date = new DateTimeImmutable("2024-01-01 00:00");
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new SafeDateTimeImmutableType())->convertToPHPValue($date->format("Y-m-d H:i"), $platform);

        $this->assertInstanceOf(DateTimeImmutable::class, $result);
        $this->assertEquals($date, $result);
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $result = (new SafeDateTimeImmutableType())->getSQLDeclaration([], $platform);

        $this->assertEquals(Types::STRING, $result);
    }
}