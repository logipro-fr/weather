<?php

namespace Weather\Tests\Application\Presenter;

use PHPUnit\Framework\TestCase;
use Weather\Application\Presenter\PresenterObject;
use Weather\Application\Presenter\ResponseInterface;

class PresenterObjectTest extends TestCase
{
    public function testPresenterReturn(): void
    {
        $expectedResponse = $this->createMock(ResponseInterface::class);
        $presenter = new PresenterObject();
        $presenter->write($expectedResponse);
        $this->assertEquals($expectedResponse, $presenter->read());
    }
}
