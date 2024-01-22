<?php

namespace Weather\Application\ImportLegacy;

use Weather\Application\Presenter\ResponseInterface;

use function Safe\json_encode;

class ImportLegacyResponse implements ResponseInterface
{
    public function __construct(private readonly int $amoutSaved)
    {
    }

    public function getData(): mixed
    {
        $array = [
            "size" => $this->amoutSaved
        ];
        return json_encode($array);
    }
}
