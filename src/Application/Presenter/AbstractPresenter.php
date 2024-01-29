<?php

namespace Weather\Application\Presenter;

abstract class AbstractPresenter
{
    protected AbstractResponse $response;

    abstract public function read(): mixed;
    abstract public function write(AbstractResponse $response): void;
    /**
     * @return array<string,string>
     */
    abstract public function getHeaders(): array;
    public function getCode(): int
    {
        return $this->response->getCode();
    }
}
