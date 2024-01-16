<?php

namespace Weather\Tests\WeatherStack\Domain\Model;

use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\HistoricalDay;
use Weather\WeatherStack\Domain\Model\HistoricalDayId;
use Weather\WeatherStack\Domain\Model\HistoricalHour;
use Weather\WeatherStack\Domain\Model\HistoricalHourId;
use Safe\DateTimeImmutable;

use function Safe\json_decode;
use function Safe\json_encode;
use function Safe\file_get_contents;

class HistoricalHourBuilder
{
    private LocationTime $locationTime;

    private function __construct(
        ?LocationTime $locationTime = null,
        private string $brutContent = "",
    ) {
        $this->locationTime = $locationTime ?: new LocationTime(
            44.039,
            4.348,
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-01-25 10:00")
        );
        $this->brutContent = file_get_contents(__DIR__ . '/resources/44.039,4.348.json');
    }

    public static function aHistoricalHour(): HistoricalHourBuilder
    {
        return new HistoricalHourBuilder();
    }

    public function withBrutContent(string $brutContent): HistoricalHourBuilder
    {
        $this->brutContent = $brutContent;
        return $this;
    }

    public function withParamSetTo(string $name, string $value): HistoricalHourBuilder
    {
        /** @var object */
        $content = json_decode($this->brutContent);

        $this->setValue($name, $value, $content);

        $this->brutContent = json_encode($content);
        return $this;
    }

    public function setValue(string $name, string $value, object $content): void
    {
        foreach ((array)$content as $attribute => $val) {
            if ($attribute === $name) {
                $content->$name = $value;
                return;
            } elseif (is_object($val)) {
                $this->setValue($name, $value, $val);
            }
        }
    }

    public function withHour(int $hour): HistoricalHourBuilder
    {
        $this->locationTime = new LocationTime(
            $this->locationTime->getPoint()->getLatitude(),
            $this->locationTime->getLongitude(),
            DateTimeImmutable::createFromFormat("Y-m-d H:i", "2023-01-25 $hour:00")
        );
        return $this;
    }

    public function build(): HistoricalHour
    {
        $id = new HistoricalHourId($this->locationTime);
        $day = new HistoricalDay(
            new HistoricalDayId(
                $this->locationTime->getPoint(),
                $this->locationTime->getTime()
            ),
            $this->brutContent
        );
        return new HistoricalHour($id, $day);
    }
}
