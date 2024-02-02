<?php

namespace Weather\Application\Presenter;

abstract class AbstractPresenter
{
    protected AbstractResponse $response;

    abstract public function read(): mixed;
    abstract public function write(AbstractResponse $response): void;
}
