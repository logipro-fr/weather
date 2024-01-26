<?php

namespace Weather\Tests\Application\Error;

use PHPUnit\Framework\TestCase;
use Weather\Application\Error\ErrorResponse;

use function Safe\json_encode;

class ErrorResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $expectedArray = [
            "code" => 404,
            "message" => "not found"
        ];
        $response = new ErrorResponse($expectedArray["code"], $expectedArray["message"]);
        $this->assertEquals($expectedArray, $response->getData());
        $this->assertEquals(json_encode($expectedArray), json_encode($response));
    }
}
