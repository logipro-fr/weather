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
}
