<?php

namespace Weather\Application\Error;

use stdClass;
use Weather\Application\Presenter\AbstractResponse;

class ErrorResponse extends AbstractResponse
{
    public function __construct(
        int $errorCode,
        private string|stdClass $message,
        private string $type
    ) {
        $this->statusCode = $errorCode;
    }

    /**
     * @return array<string,int|string|stdClass>
     */
    public function getData(): array
    {
        return ["code" => $this->statusCode, "type" => $this->type, "error" => $this->message];
    }
}
