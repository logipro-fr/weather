<?php

namespace Weather\Domain\Model\Exceptions;

class WeatherInfoNotFoundException extends BaseException
{
    protected string $type = "weatherinfo_not_found_exception";
}
