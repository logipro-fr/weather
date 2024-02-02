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
        $response = $route->execute(new Request());

        /** @var string $str */
        $str = $response->getContent();
        $this->assertStringStartsWith('{"Hello":"', $str);
        $this->assertStringEndsWith('"}', $str);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("application/json", $response->headers->get("Content-Type"));
    }
}
