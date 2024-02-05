<?php

namespace Weather\Application\Error;

use JsonSerializable;
use Weather\Application\Presenter\AbstractResponse;

class ErrorResponse extends AbstractResponse
{
    public function __construct(
        int $errorCode,
        private string $message,
        private string $type,
        private mixed $data = null
    ) {
        $this->statusCode = $errorCode;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function getError(): ?string
    {
        return $this->type;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
