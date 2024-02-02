<?php

namespace Weather\Application\Presenter;

abstract class ApiPresenter extends AbstractPresenter
{
    abstract public function read(): string;
    /**
     * @return array<string,string>
     */
    abstract public function getHeaders(): array;
    public function getCode(): int
    {
        return $this->response->getCode();
    }
}
