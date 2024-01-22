<?php

namespace Weather\Application\ImportLegacy;

class ImportLegacyRequest
{
    public function __construct(private readonly string $path)
    {
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
