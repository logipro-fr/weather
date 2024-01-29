<?php

namespace Weather\Tests\Application\Presenter;

use PHPUnit\Framework\TestCase;
use Weather\Application\Presenter\PresenterObject;
use Weather\Application\Presenter\AbstractResponse;

class PresenterObjectTest extends TestCase
{
    public function testPresenterReturn(): void
    {
        $expectedResponse = $this->createMock(AbstractResponse::class);
        $presenter = new PresenterObject();
        $presenter->write($expectedResponse);
        $this->assertEquals($expectedResponse, $presenter->read());
        $this->assertEquals(["Content-Type" => "text/plain"], $presenter->getHeaders());
        $this->assertEquals(200, $presenter->getCode());
        $presenter->writeSatusCode(418);
        $this->assertEquals(418, $presenter->getCode());
    }
}
