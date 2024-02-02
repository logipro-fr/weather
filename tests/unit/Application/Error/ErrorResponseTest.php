<?php

namespace Weather\Tests\Application\Error;

use PHPUnit\Framework\TestCase;
use Weather\Application\Error\ErrorResponse;
use Weather\Application\Presenter\PresenterJson;

use function Safe\json_encode;

class ErrorResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $expectedArray = [
            "success" => false,
            "data" => null,
            "errorCode" => "i_m_a_teapot",
            "message" => "I'm a teapot and therefore am unable to brew coffee"
        ];
        $response = new ErrorResponse(418, $expectedArray["message"], $expectedArray["errorCode"]);
        $this->assertEquals(null, $response->getData());
        $presenter = new PresenterJson();
        $presenter->write($response);
        $this->assertEquals(json_encode($expectedArray), $presenter->read());
    }
    public function testResponseObject(): void
    {
        $expectedArray = [
            "success" => false,
            "data" => json_decode('{"a":"I\'m a teapot and","b":"therefore am unable to brew coffee"}'),
            "errorCode" => "i_m_a_teapot",
            "message" => "I'm a teapot and therefore am unable to brew coffee"
        ];
        $response = new ErrorResponse(
            418, 
            $expectedArray["message"], 
            $expectedArray["errorCode"], 
            $expectedArray["data"]
        );
        $presenter = new PresenterJson();
        $presenter->write($response);
        $this->assertEquals(json_encode($expectedArray), $presenter->read());
    }
}
