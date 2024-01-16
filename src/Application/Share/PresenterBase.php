<?php

namespace Weather\Application\Share;

class PresenterBase implements PresenterInterface
{
    /** @var array<mixed> */
    private array $data = [];

    public function write(Response $response): void
    {
        $this->data = (array)$response;
    }

    /**
     * @return array<mixed>
     */
    public function read(): array
    {
        return $this->data;
    }
}
