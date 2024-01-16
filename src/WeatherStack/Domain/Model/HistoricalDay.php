<?php

namespace Weather\WeatherStack\Domain\Model;

use Weather\Share\Domain\LocationTime;
use Weather\WeatherStack\Domain\Model\Exceptions\OutOfRangeDayHourException;
use Weather\WeatherStack\Application\Service\HistoricalWeatherInterface;
use Safe\DateTimeImmutable;

use function Safe\json_decode;
use function Safe\json_encode;

class HistoricalDay implements HistoricalWeatherInterface
{
    private const FIRST_DAY_HOUR = 0;
    private const LAST_DAY_HOUR = 23;

    public function __construct(
        private HistoricalDayId $id,
        private string $brutContent,
    ) {
    }

    public function makeHistoricalHour(int $dayHour): HistoricalHour
    {
        $this->checkDayHour($dayHour);
        $dateOfHistoricalHour = DateTimeImmutable::createFromFormat(
            "Y-m-d H",
            $this->id->getHistoricalDate()->format("Y-m-d ") . strval($dayHour)
        );
        $id = new HistoricalHourId(new LocationTime(
            $this->id->getPoint()->getLatitude(),
            $this->id->getPoint()->getLongitude(),
            $dateOfHistoricalHour
        ));
        return new HistoricalHour($id, $this);
    }

    private function checkDayHour(int $dayHour): void
    {
        if (($dayHour < self::FIRST_DAY_HOUR) || ($dayHour > self::LAST_DAY_HOUR)) {
            throw new OutOfRangeDayHourException(
                "Day hour must be a integer value between 0-23. '" . intval($dayHour) . "' is out of range."
            );
        }
    }

    public function getId(): HistoricalDayId
    {
        return $this->id;
    }

    public function getHistoricalDate(): DateTimeImmutable
    {
        return $this->id->getHistoricalDate();
    }

    public function getBrutContent(): string
    {
        return $this->brutContent;
    }

    public function getMoonIllumination(): int
    {
        return $this->getContent()->astro->moon_illumination;
    }

    private function getContent(): \stdClass
    {
        /** @var \stdClass */
        $content = json_decode($this->brutContent);
        $date = $this->getHistoricalDate()->format("Y-m-d");
        return $content->historical->$date;
    }
    public function getTotalSnow(): float
    {
        return $this->getContent()->totalsnow;
    }
    public function getSunHour(): float
    {
        return $this->getContent()->sunhour;
    }
    public function getSunSet(): string
    {
        return $this->getContent()->astro->sunset;
    }
    public function getSunRise(): string
    {
        return $this->getContent()->astro->sunrise;
    }
}
