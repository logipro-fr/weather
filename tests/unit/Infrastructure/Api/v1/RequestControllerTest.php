<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\InputBag;
use Weather\Application\ServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Weather\Application\Presenter\PresenterJson;
use Weather\Domain\Model\Exceptions\ApiException;
use Weather\Domain\Model\Exceptions\BaseException;
use Weather\Application\Presenter\RequestInterface;
use Weather\Tests\Infrastructure\Api\v1\FakeRequestController;

use function PHPUnit\Framework\assertEquals;
use function Safe\json_encode;

class RequestControllerTest extends TestCase
{
    public function testFailure(): void
    {
        $service = $this->createMock(ServiceInterface::class);
        $request = $this->createMock(RequestInterface::class);

        $service->method("getPresenter")->willReturn(new PresenterJson());
        $service->method("execute")->willThrowException(
            new BaseException("the server refuses to brew coffee because it is, permanently, a teapot.", 418)
        );

        $controller = new FakeRequestController($service, $request);

        $request = new Request([]);
        $response = $controller->execute($request);

        $target = [
            "success" => false,
            "data" => null,
            "errorCode" => "exception",
            "message" => "the server refuses to brew coffee because it is, permanently, a teapot."
        ];

        assertEquals(json_encode($target), $response->getContent());
    }

    public function testFailureComplex(): void
    {
        $service = $this->createMock(ServiceInterface::class);

        $controller = new FakeRequestController($service, $this->createMock(RequestInterface::class));

        $service->method("getPresenter")->willReturn(new PresenterJson());
        $service->method("execute")->willThrowException(
            new BaseException(
                "the server refuses to brew coffee because it is, permanently, a teapot.",
                418,
                json_decode('{"a":"the server refuses to brew coffee",' .
                    '"b":"because it is, permanently, a teapot."}')
            )
        );

        $request = new Request([]);
        $response = $controller->execute($request);

        $target = [
            "success" => false,
            "data" => [
                "a" => "the server refuses to brew coffee",
                "b" => "because it is, permanently, a teapot."
            ],
            "errorCode" => "exception",
            "message" => "the server refuses to brew coffee because it is, permanently, a teapot."
        ];
        assertEquals(json_encode($target), $response->getContent());
    }

    public function testReadStatus1(): void
    {
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new BaseException("message", 99));
        $presenter = new PresenterJson();
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new FakeRequestController($service, $this->createMock(RequestInterface::class));
        $request = new Request([]);
        $response = $controller->execute($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testReadStatus2(): void
    {
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new BaseException("message", 700));
        $presenter = new PresenterJson();
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new FakeRequestController($service, $this->createMock(RequestInterface::class));
        $request = new Request([]);
        $response = $controller->execute($request);

        $this->assertEquals(500, $response->getStatusCode());
    }

    public function testReadStatus3(): void
    {
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new BaseException("message", 100));
        $presenter = new PresenterJson();
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new FakeRequestController($service, $this->createMock(RequestInterface::class));
        $request = new Request([]);
        $response = $controller->execute($request);

        $this->assertEquals(100, $response->getStatusCode());
    }

    public function testReadStatus4(): void
    {
        $service = $this->createMock(ServiceInterface::class);
        $service->method("execute")->willThrowException(new BaseException("message", 599));
        $presenter = new PresenterJson();
        $service->method("getPresenter")->willReturn($presenter);

        $controller = new FakeRequestController($service, $this->createMock(RequestInterface::class));
        $request = new Request([]);
        $response = $controller->execute($request);

        $this->assertEquals(599, $response->getStatusCode());
    }
}
