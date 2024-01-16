<?php

namespace Weather\Tests\Application\Shared;

use Weather\Application\Share\Response;

class ResponseFake implements Response
{
    public function __construct(
        public readonly string $myResponse
    ) {
    }
}
