<?php

namespace Weather\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Weather\Domain\Model\Weather\Point;
use Weather\Infrastructure\Persistence\Doctrine\Types\PointType;

class PointTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals("point", (new PointType())->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $id = new Point(3.621, 69.420);
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new PointType())->convertToDatabaseValue($id, $platform);

        $this->assertIsString($result);
        $this->assertEquals($id->__toString(), $result);
    }

    public function testConvertToPHPValue(): void
    {
        $id = "3.621,69.420";
        $target = new Point(3.621, 69.420);
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new PointType())->convertToPHPValue($id, $platform);
        
        $this->assertInstanceOf(Point::class, $result);
        $this->assertEquals($target, $result);
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $result = (new PointType())->getSQLDeclaration([], $platform);

        $this->assertEquals(Types::STRING, $result);
    }
}
