<?php

namespace Weather\Tests\Application\ImportLegacy;

use PHPUnit\Framework\TestCase;
use Weather\Application\ImportLegacy\ImportLegacyResponse;

use function Safe\json_encode;

class ImportLegacyResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $expectedArray = [
            "size" => 25
        ];
        $response = new ImportLegacyResponse(25);
        $this->assertEquals($expectedArray, $response->getData());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals(json_encode($expectedArray), json_encode($response));
    }
}
