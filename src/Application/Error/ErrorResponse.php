<?php

namespace Weather\Application\Error;

use Weather\Application\Presenter\AbstractResponse;

class ErrorResponse extends AbstractResponse
{
    public function __construct(
        private int $errorCode,
        private string $message
    ) {
    }

    /**
     * @return array<string,int|string>
     */
    public function getData(): array
    {
        return ["code" => $this->errorCode, "message" => $this->message];
    }
}
