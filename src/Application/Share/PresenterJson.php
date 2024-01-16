<?php

namespace Weather\Application\Share;

class PresenterJson implements PresenterInterface
{
    private Response $response;

    public function write(Response $response): void
    {
        $this->response = $response;
    }

    public function read()
    {
        $json = json_encode($this->response, JSON_PRETTY_PRINT);
        return $json;
    }
}
