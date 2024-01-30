<?php

namespace Weather\Application\Presenter;

class PresenterObject extends AbstractPresenter
{
    public function read(): AbstractResponse
    {
        return $this->response;
    }

    public function write(AbstractResponse $response): void
    {
        $this->response = $response;
    }

    public function getHeaders(): array
    {
        return ["Content-Type" => "text/plain"];
    }
}
