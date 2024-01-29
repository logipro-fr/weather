<?php

namespace Weather\Domain\Model\Weather;

use JsonSerializable;

class WeatherInfoId implements JsonSerializable
{
    private string $id;

    public const PREFIX_NAME = "weather_";
    public const CHAR_AMOUNT = 32; // does not include prefix
    public const BYTE_PER_CHAR = 2;
    public const SIZE = WeatherInfoId::CHAR_AMOUNT / WeatherInfoId::BYTE_PER_CHAR;

    public function __construct(?string $id = null)
    {
        $this->id = $id == null ? WeatherInfoId::generateId() : $id;
    }

    private static function generateId(): string
    {
        $value = bin2hex(random_bytes(intval(WeatherInfoId::SIZE)));
        return (WeatherInfoId::PREFIX_NAME . $value);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function equals(WeatherInfoId $other): bool
    {
        return $this->getId() == $other->getId();
    }

    public function __toString(): string
    {
        return $this->getId();
    }

    public function jsonSerialize(): mixed
    {
        return $this->getId();
    }
}
