<?php

namespace Weather\Application\Error;

use Weather\Application\Presenter\AbstractResponse;

class ErrorResponse extends AbstractResponse
{
    public function __construct(
        int $errorCode,
        private string $message
    ) {
        $this->statusCode = $errorCode;
    }

    /**
     * @return array<string,int|string>
     */
    public function getData(): array
    {
        return ["code" => $this->statusCode, "message" => $this->message];
    }
}
