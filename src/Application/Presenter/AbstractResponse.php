<?php

namespace Weather\Application\Presenter;

use JsonSerializable;

abstract class AbstractResponse implements JsonSerializable
{
    abstract public function getData(): mixed;

    public function jsonSerialize(): mixed
    {
        return $this->getData();
    }
}
