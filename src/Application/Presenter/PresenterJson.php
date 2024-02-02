<?php

namespace Weather\Application\Presenter;

use function Safe\json_encode;

class PresenterJson extends ApiPresenter
{
    public function read(): string
    {
        return json_encode($this->response, JSON_UNESCAPED_UNICODE);
    }

    public function write(AbstractResponse $response): void
    {
        $this->response = $response;
    }

    public function getHeaders(): array
    {
        return ["Content-Type" => "application/json"];
    }
}
