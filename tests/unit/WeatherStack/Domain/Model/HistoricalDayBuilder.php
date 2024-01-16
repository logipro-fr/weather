<?php

namespace Weather\Tests\WeatherStack\Domain\Model;

use Weather\Share\Domain\Point;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Safe\DateTimeImmutable;

use function Safe\file_get_contents;

class HistoricalDayBuilder
{
    private function __construct(
        private Point $location = new Point(44.039, 4.348),
        private DateTimeImmutable $historicalDate = new DateTimeImmutable(),
        private string $brutContent = "",
    ) {
        $this->historicalDate = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-01-25 10:00");

        $this->brutContent = file_get_contents(__DIR__ . '/resources/44.039,4.348.json');
    }

    public static function aHistoricalDay(): HistoricalDayBuilder
    {
        return new HistoricalDayBuilder();
    }

    public function withBrutContent(string $brutContent): HistoricalDayBuilder
    {
        $this->brutContent = $brutContent;
        return $this;
    }

    public function build(): HistoricalDay
    {
        $id = new HistoricalDayId($this->location, $this->historicalDate);
        return new HistoricalDay($id, $this->brutContent);
    }
}
