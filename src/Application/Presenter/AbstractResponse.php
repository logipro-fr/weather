<?php

namespace Weather\Application\Presenter;

use JsonSerializable;

abstract class AbstractResponse implements JsonSerializable
{
    protected int $statusCode = 200;

    abstract public function getData(): mixed;

    public function jsonSerialize(): mixed
    {
        return $this->getData();
    }

    public function getCode(): int
    {
        return $this->statusCode;
    }
}
