<?php

namespace Weather\Tests\Application\Presenter;

use PHPUnit\Framework\TestCase;
use Weather\Application\Presenter\PresenterJson;
use Weather\Application\Presenter\AbstractResponse;

use function Safe\json_encode;

class PresenterJsonTest extends TestCase
{
    public function testPresenterReturn(): void
    {
        $response = $this->createMock(AbstractResponse::class);
        $target = ["success" => true,"data" => [
            "first" => 1,
            "second" => "two",
            "third" => [
                "a",
                "b",
                "c"
            ]
            ],"errorCode" => null,"message" => null];
        $response->method("jsonSerialize")->willReturn($target["data"]);
        $response->method("getCode")->willReturn(200);
        $presenter = new PresenterJson();
        $presenter->write($response);

        $this->assertEquals(json_encode($target), $presenter->read());
        $this->assertEquals(["Content-Type" => "application/json"], $presenter->getHeaders());
    }

    public function testPresenterSucess1(): void
    {
        $target = 200;

        $response = $this->createMock(AbstractResponse::class);
        $response->method("getCode")->willReturn($target);
        $presenter = new PresenterJson();

        $presenter->write($response);

        /** @var object{"success": bool} */
        $res = json_decode($presenter->read());
        $this->assertTrue($res->success);
    }

    public function testPresenterSucess2(): void
    {
        $target = 299;

        $response = $this->createMock(AbstractResponse::class);
        $response->method("getCode")->willReturn($target);
        $presenter = new PresenterJson();

        $presenter->write($response);

        /** @var object{"success": bool} */
        $res = json_decode($presenter->read());
        $this->assertTrue($res->success);
    }

    public function testPresenterFail1(): void
    {
        $target = 199;

        $response = $this->createMock(AbstractResponse::class);
        $response->method("getCode")->willReturn($target);
        $presenter = new PresenterJson();

        $presenter->write($response);

        /** @var object{"success": bool} */
        $res = json_decode($presenter->read());
        $this->assertFalse($res->success);
    }

    public function testPresenterFail2(): void
    {
        $target = 300;

        $response = $this->createMock(AbstractResponse::class);
        $response->method("getCode")->willReturn($target);
        $presenter = new PresenterJson();

        $presenter->write($response);

        /** @var object{"success": bool} */
        $res = json_decode($presenter->read());
        $this->assertFalse($res->success);
    }
}
