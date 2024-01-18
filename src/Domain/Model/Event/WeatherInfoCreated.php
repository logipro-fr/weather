<?php

namespace Weather\Domain\Model\Event;

use Phariscope\Event\EventAbstract;

class WeatherInfoCreated extends EventAbstract
{
    public function __construct(public readonly string $id)
    {
    }
}
