<?php

namespace Weather\Application\Presenter;

abstract class AbstractPresenter
{
    private int $statusCode = 200;

    abstract public function read(): mixed;
    abstract public function write(AbstractResponse $responce): void;
    /**
     * @return array<string,string>
     */
    abstract public function getHeaders(): array;
    public function getCode(): int
    {
        return $this->statusCode;
    }
    public function writeSatusCode(int $code): void
    {
        $this->statusCode = $code;
    }
}
