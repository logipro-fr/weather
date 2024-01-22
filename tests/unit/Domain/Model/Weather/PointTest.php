<?php

namespace Weather\Tests\Domain\Model\Weather;

use Weather\Domain\Model\Weather\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testCreatePoint(): void
    {
        $point = new Point(1, 2);

        $this->assertEquals(1, $point->getLatitude());
        $this->assertEquals(2, $point->getLongitude());
    }

    public function testCreatePoint1(): void
    {
        $point = new Point(1.5, 2.5);

        $this->assertEquals(1.5, $point->getLatitude());
        $this->assertEquals(2.5, $point->getLongitude());
    }

    public function testEquals(): void
    {
        $pointA = new Point(1.5, 2.5);
        $pointB = new Point(1.5, 2.5);

        $this->assertTrue($pointB->equals($pointA));
    }

    public function testUnequals(): void
    {
        $pointA = new Point(1.5, 2.5);
        $pointB = new Point(1.5, 2.0);
        $pointC = new Point(2.0, 2.5);
        $pointD = new Point(2.0, 2.0);

        $this->assertFalse($pointB->equals($pointA));
        $this->assertFalse($pointC->equals($pointA));
        $this->assertFalse($pointD->equals($pointA));
    }

    public function testToString(): void
    {
        $point = new Point(1.5, 2.5);

        $this->assertEquals("1.5,2.5", $point->__toString());
    }
}
