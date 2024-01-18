<?php

namespace Weather\Application\Presenter;

interface PresenterInterface
{
    public function read(): mixed;
    public function write(ResponseInterface $responce): void;
}
