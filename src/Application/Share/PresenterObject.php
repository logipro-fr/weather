<?php

namespace Weather\Application\Share;

class PresenterObject implements PresenterInterface
{
    private Response $data;

    public function write(Response $response): void
    {
        $this->data = $response;
    }

    public function read(): Response
    {
        return $this->data;
    }
}
