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

    public function testToString():void{
        $point= new Point(12.5,35.486);

        $this->assertEquals("12.5,35.486", $point->__toString());
    }
}
