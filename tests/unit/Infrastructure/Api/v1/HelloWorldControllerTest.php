<?php

namespace Weather\Tests\Infrastructure\Api\v1;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Weather\Infrastructure\Api\v1\Symfony\HelloWorldController;

class HelloWorldControllerTest extends TestCase
{
    public function testHelloWorld(): void
    {
        $route = new HelloWorldController();
        $response = $route->helloWorld(new Request());

        $this->assertEquals('{"Hello":"World!"}', $response->getContent());
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
