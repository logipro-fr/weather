<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use Exception;
use PHPUnit\Framework\TestCase;
use Weather\Application\Error\ErrorResponse;
use Weather\Application\Presenter\PresenterJson;
use Weather\Application\Presenter\PresenterObject;
use Weather\Application\Presenter\RequestInterface;
use Weather\Application\ServiceInterface;
use Weather\Domain\Model\Exceptions\BaseException;
use Weather\Infrastructure\Api\v1\Controller;

use function PHPUnit\Framework\assertEquals;
use function Safe\json_encode;

class ControllerTest extends TestCase
{
    public function testFailure(): void
    {
        $service = $this->createMock(ServiceInterface::class);

        $controller = new Controller($service);

        $service->method("getPresenter")->willReturn(new PresenterJson());
        $service->method("execute")->willThrowException(
            new BaseException("the server refuses to brew coffee because it is, permanently, a teapot.", 418)
        );

        $controller->execute($this->createMock(RequestInterface::class));

        $target = new ErrorResponse(
            418,
            "the server refuses to brew coffee because it is, permanently, a teapot."
        );
        assertEquals(json_encode($target), $controller->readResponse());
    }

    public function testReadStatus1(){
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new Exception("message",99));
        $presenter = new PresenterObject;
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new Controller($service);
        $controller->execute($this->createMock(RequestInterface::class));

        $this->assertEquals(500, $controller->readStatus());
    }

    public function testReadStatus2(){
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new Exception("message",600));
        $presenter = new PresenterObject;
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new Controller($service);
        $controller->execute($this->createMock(RequestInterface::class));

        $this->assertEquals(500, $controller->readStatus());
    }

    public function testReadStatus3(){
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new Exception("message",100));
        $presenter = new PresenterObject;
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new Controller($service);
        $controller->execute($this->createMock(RequestInterface::class));

        $this->assertEquals(100, $controller->readStatus());
    }

    public function testReadStatus4(){
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new Exception("message", 599));
        $presenter = new PresenterObject;
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new Controller($service);
        $controller->execute($this->createMock(RequestInterface::class));

        $this->assertEquals(599, $controller->readStatus());
    }
}
