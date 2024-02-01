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
            "code" => 418,
            "type" => "i_m_a_teapot",
            "error" => "I'm a teapot and therefore am unable to brew coffee"
        ];
        $response = new ErrorResponse($expectedArray["code"], $expectedArray["error"], $expectedArray["type"]);
        $this->assertEquals($expectedArray, $response->getData());
        $this->assertEquals(json_encode($expectedArray), json_encode($response));
    }
    public function testResponseObject(): void
    {
        $expectedArray = [
            "code" => 418,
            "type" => "i_m_a_teapot",
            "error" => json_decode('{"a":"I\'m a teapot and","b":"therefore am unable to brew coffee"}')
        ];
        $response = new ErrorResponse($expectedArray["code"], $expectedArray["error"], $expectedArray["type"]);
        $this->assertEquals($expectedArray, $response->getData());
        $this->assertEquals(json_encode($expectedArray), json_encode($response));
    }
}
