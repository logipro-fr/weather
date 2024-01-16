<?php

namespace Weather\WeatherStack\Domain\Model;

use Weather\WeatherStack\Application\Service\HistoricalWeatherInterface;
use Safe\DateTime;
use Safe\DateTimeImmutable;

use function Safe\json_decode;
use function Safe\preg_match;

class HistoricalHour implements HistoricalWeatherInterface
{
    const FIRST_DAY_HOUR = 0;
    const LAST_DAY_HOUR = 23;

    public function __construct(
        private HistoricalHourId $id,
        private HistoricalDay $day,
    ) {
    }

    public function getId(): HistoricalHourId
    {
        return $this->id;
    }

    public function getHistoricalDate(): DateTimeImmutable
    {
        return $this->id->getHistoricalDate();
    }

    public function getMoonIllumination(): int
    {
        return $this->getHistoricalContent()->astro->moon_illumination;
    }

    public function getSunRise(): string
    {
        return $this->getHistoricalContent()->astro->sunrise;
    }

    public function getSunSet(): string
    {
        return $this->getHistoricalContent()->astro->sunset;
    }

    public function getMoonRise(): string
    {
        return $this->getHistoricalContent()->astro->moonrise;
    }

    public function getMoonSet(): string
    {
        return $this->getHistoricalContent()->astro->moonset;
    }

    private function getHistoricalContent(): \stdClass
    {
        $content = $this->getContent();
        $date = $this->getHistoricalDate()->format("Y-m-d");
        return $content->historical->$date;
    }

    public function getTotalSnow(): float
    {
        return $this->getHistoricalContent()->totalsnow;
    }

    public function getSunHour(): float
    {
        return $this->getHistoricalContent()->sunhour;
    }

    public function get(string $name): mixed
    {
        $others = [
            'getMoonIllumination' => 'moon_illumination',
            'getTotalSnow' => 'totalsnow',
            'getSunHour' => 'sunhour',
            'getSunRise' => 'sunrise',
            'getSunSet' => 'sunset',
            'getMoonRise' => 'moonrise',
            'getMoonSet' => 'moonset',
            'getMoonOn' => 'moonon',
            'getSunOn' => 'sunon',
        ];
        if (in_array($name, $others)) {
            $flip = array_flip($others);
            $getName = $flip[$name];
            return $this->$getName();
        }
        $hour = $this->getHour();
        return $hour->$name;
    }

    public function getTemperature(): int
    {
        $hour = $this->getHour();
        return $hour->temperature;
    }

    private function getHour(): \stdClass
    {
        $content = $this->getContent();
        $date = $this->getHistoricalDate()->format("Y-m-d");
        $hourly = $content->historical->$date->hourly;

        return $hourly[intval($this->getHistoricalDate()->format("H"))];
    }

    private function getContent(): \stdClass
    {
        /** @var \stdClass */
        $content = json_decode($this->day->getBrutContent());
        return $content;
    }

    public function getWindSpeed(): int
    {
        $hour = $this->getHour();
        return $hour->wind_speed;
    }
    public function getWindDegree(): int
    {
        $hour = $this->getHour();
        return $hour->wind_degree;
    }
    public function getWindDir(): string
    {
        $hour = $this->getHour();
        return $hour->wind_dir;
    }
    public function getWeatherCode(): int
    {
        $hour = $this->getHour();
        return $hour->weather_code;
    }

    /**
     * @return array<string>
     */
    public function getWeatherIcons(): array
    {
        $hour = $this->getHour();
        return $hour->weather_icons;
    }

    /**
     * @return array<string>
     */
    public function getWeatherDescriptions(): array
    {
        $hour = $this->getHour();
        return $hour->weather_descriptions;
    }
    public function getPrecip(): int
    {
        $hour = $this->getHour();
        return $hour->precip;
    }
    public function getHumidity(): int
    {
        $hour = $this->getHour();
        return $hour->humidity;
    }
    public function getVisibility(): int
    {
        $hour = $this->getHour();
        return $hour->visibility;
    }
    public function getPressure(): int
    {
        $hour = $this->getHour();
        return $hour->pressure;
    }
    public function getCloudCover(): int
    {
        $hour = $this->getHour();
        return $hour->cloudcover;
    }
    public function getHeatIndex(): int
    {
        $hour = $this->getHour();
        return $hour->heatindex;
    }
    public function getDewPoint(): int
    {
        $hour = $this->getHour();
        return $hour->dewpoint;
    }
    public function getiWndChill(): int
    {
        $hour = $this->getHour();
        return $hour->windchill;
    }
    public function getWindGust(): int
    {
        $hour = $this->getHour();
        return $hour->windgust;
    }
    public function getfeelslike(): int
    {
        $hour = $this->getHour();
        return $hour->feelslike;
    }
    public function getChanceOfRain(): int
    {
        $hour = $this->getHour();
        return $hour->chanceoffog;
    }
    public function getChanceOfRemdry(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofremdry;
    }
    public function getChanceOfWindy(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofwindy;
    }
    public function getchanceofovercast(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofovercast;
    }
    public function getChanceOfSunshine(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofsunshine;
    }
    public function getChanceOfFrost(): int
    {
        $hour = $this->getHour();
        return $hour->chanceoffrost;
    }
    public function getChanceOfHighTemp(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofhightemp;
    }
    public function getChanceOfFog(): int
    {
        $hour = $this->getHour();
        return $hour->chanceoffog;
    }
    public function getChanceOfSnow(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofsnow;
    }
    public function getchanceofthunder(): int
    {
        $hour = $this->getHour();
        return $hour->chanceofthunder;
    }
    public function getUvIndex(): int
    {
        $hour = $this->getHour();
        return $hour->uv_index;
    }

    public function getSunOn(): int
    {
        $sunrise = new DateTime($this->getSunRise());
        $sunset = new DateTime($this->getSunSet());

        $hour = $this->getId()->getHistoricalDate()->format("H");
        $currentTime = DateTime::createFromFormat('H', $hour);

        return intval($currentTime >= $sunrise && $currentTime <= $sunset);
    }

    public function getMoonOn(): int
    {
        $pattern = "/^(0[1-9]|1[0-2]):[0-5][0-9] (AM|PM)$/i";

        $moonrise = preg_match($pattern, $this->getMoonRise()) ?
            new DateTime($this->getMoonRise()) :
            DateTime::createFromFormat("H:i", "00:00");

        $moonset = preg_match($pattern, $this->getMoonSet()) ?
            new DateTime($this->getMoonSet()) :
            new DateTime("11:59 PM");

        $hour = $this->getId()->getHistoricalDate()->format("H");
        $currentTime = DateTime::createFromFormat('H', $hour);

        $lastMinute = DateTime::createFromFormat('H:i:s', '23:59:59');

        if ($moonrise > $moonset) {
            return intval(($currentTime >= $moonrise && $currentTime < $lastMinute) || ($currentTime <= $moonset));
        }

        return intval($currentTime >= $moonrise && $currentTime <= $moonset);
    }
}
