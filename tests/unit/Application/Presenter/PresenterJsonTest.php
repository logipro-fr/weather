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
        $target = [
            "first" => 1,
            "second" => "two",
            "third" => [
                "a",
                "b",
                "c"
            ]
            ];
        $response->method("jsonSerialize")->willReturn($target);
        $presenter = new PresenterJson();
        $presenter->write($response);
        $this->assertEquals(json_encode($target), $presenter->read());
        $this->assertEquals(["Content-Type" => "application/json"], $presenter->getHeaders());
    }
}
