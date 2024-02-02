<?php

namespace Weather\Application\Presenter;

use function Safe\json_encode;

class PresenterJson extends ApiPresenter
{
    public function read(): string
    {
        return json_encode([
            "success" => $this->getCode() >=200 && $this->getCode( )< 300,
            "data" => $this->response,
            "errorCode" => $this->response->getError(),
            "message" => $this->response->getMessage()
        ], JSON_UNESCAPED_UNICODE);
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
