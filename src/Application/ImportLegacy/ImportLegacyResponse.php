<?php

namespace Weather\Application\ImportLegacy;

use Weather\Application\Presenter\AbstractResponse;

class ImportLegacyResponse extends AbstractResponse
{
    public function __construct(private readonly int $amoutSaved)
    {
    }

    public function getData(): mixed
    {
        $array = [
            "size" => $this->amoutSaved
        ];
        return $array;
    }
}
