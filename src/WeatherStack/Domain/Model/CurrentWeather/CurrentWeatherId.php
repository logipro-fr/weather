<?php

namespace Weather\WeatherStack\Domain\Model\CurrentWeather;

class CurrentWeatherId
{
    private string $uid;

    /**
     * @param string|null $uid
     */
    public function __construct(string $uid = null)
    {
        $this->uid = $uid ?: uniqid();
    }

    public function getId(): string
    {
        return $this->uid;
    }

    public function equals(CurrentWeatherId $anId): bool
    {
        return $this->uid === $anId->getId();
    }

    public function __toString()
    {
        return $this->getId();
    }
}
