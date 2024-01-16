<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Persistence\HistoricalDay;

use Weather\Share\Domain\Point;
use Weather\WeatherStack\Infrastructure\Persistence\HistoricalDay\StoreFileName;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;

class StoreFileNameTest extends TestCase
{
    public function testFileName(): void
    {
        $date =  DateTimeImmutable::createFromFormat("Y/m/d H:i", "2023/10/25 8:01");
        $id = new HistoricalDayId(new Point(-1.234, 4.567), $date);

        $filename = StoreFileName::getFileName($id);
        $this->assertEquals("P-1.234,4.567", $filename);
    }

    public function testPath(): void
    {
        $date =  DateTimeImmutable::createFromFormat("Y/m/d H:i", "2023/10/25 8:01");
        $id = new HistoricalDayId(new Point(-1.234, 4.567), $date);

        $path = StoreFileName::getPath($id);
        $this->assertEquals("vfs://VFSDir/documents/weatherstack/2023/10/25", $path);
    }

    public function testFullFileName(): void
    {
        $date =  DateTimeImmutable::createFromFormat("Y/m/d H:i", "2023/10/25 8:01");
        $id = new HistoricalDayId(new Point(-1.234, 4.567), $date);

        $fullName = StoreFileName::getFullFileName($id);
        $this->assertEquals("vfs://VFSDir/documents/weatherstack/2023/10/25/P-1.234,4.567", $fullName);
    }
}
