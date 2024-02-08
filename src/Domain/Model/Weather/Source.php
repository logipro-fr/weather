<?php

namespace Weather\Domain\Model\Weather;

use JsonSerializable;

enum Source : string implements JsonSerializable
{
    case DEBUG = "debug";
    case WEATHERSTACK = "WeatherStack";

    public function getName(): string
    {
        return $this->value;
    }

    public function getUrl(): string
    {
        switch ($this) {
            case (self::WEATHERSTACK):
                return "https://api.weatherstack.com/";
            default:
                return "debug";
        }
    }

    public static function createFromName(string $name): Source
    {
        switch ($name) {
            case self::WEATHERSTACK->getName():
                return self::WEATHERSTACK;
            default:
                return self::DEBUG;
        }
    }

    public function jsonSerialize(): mixed
    {
        return [
            "name"  => $this->getName(),
            "url"   => $this->getUrl()
        ];
    }
}
