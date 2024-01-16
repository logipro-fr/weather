<?php

namespace Weather\Tests\Share\Domain;

use Weather\Share\Domain\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testDistance(): void
    {
        $point1 = new Point(45.5, 12.2);
        $point2 = new Point(45.5, 12.2);
        $this->assertEquals(0, $point1->distance($point2));
        $point2 = new Point(45.5, 12.3);
        $this->assertEquals(7793.75, round($point1->distance($point2), 2));
        $point2 = new Point(45.6, 12.2);
        $this->assertEquals(11119.49, round($point1->distance($point2), 2));
        $point2 = new Point(45.6, 12.3);
        $this->assertEquals(13574.9, round($point1->distance($point2), 2));
    }
}
