<?php

namespace Weather\Application\Share;

interface PresenterInterface
{
    public function write(Response $response): void;

    /**
     * @return mixed
     */
    public function read();
}
