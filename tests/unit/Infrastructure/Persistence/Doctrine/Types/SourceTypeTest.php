<?php

namespace Weather\Tests\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;
use Weather\Domain\Model\Weather\Point;
use Weather\Domain\Model\Weather\Source;
use Weather\Infrastructure\Persistence\Doctrine\Types\SourceType;

class SourceTypeTest extends TestCase
{
    public function testGetName(): void
    {
        $this->assertEquals("source", (new SourceType())->getName());
    }

    public function testConvertToDatabaseValue(): void
    {
        $id = Source::DEBUG;
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new SourceType())->convertToDatabaseValue($id, $platform);

        $this->assertIsString($result);
        $this->assertEquals($id->getName(), $result);
    }

    public function testConvertToPHPValue(): void
    {
        $id = "debug";
        $target = Source::DEBUG;
        $platform = $this->createMock(AbstractPlatform::class);

        $result = (new SourceType())->convertToPHPValue($id, $platform);

        $this->assertInstanceOf(Source::class, $result);
        $this->assertEquals($target, $result);
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = $this->createMock(AbstractPlatform::class);
        $result = (new SourceType())->getSQLDeclaration([], $platform);

        $this->assertEquals(Types::TEXT, $result);
    }
}
