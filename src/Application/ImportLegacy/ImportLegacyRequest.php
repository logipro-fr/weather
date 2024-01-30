<?php

namespace Weather\Application\ImportLegacy;

use Weather\Application\Presenter\RequestInterface;

class ImportLegacyRequest implements RequestInterface
{
    public function __construct(private readonly string $path)
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
