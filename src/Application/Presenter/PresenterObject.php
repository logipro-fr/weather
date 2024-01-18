<?php

namespace Weather\Application\Presenter;

class PresenterObject implements PresenterInterface
{
    private ResponseInterface $response;

    public function read(): ResponseInterface
    {
        return $this->response;
    }

    public function write(ResponseInterface $response): void
    {
        $this->response = $response;
    }
}
