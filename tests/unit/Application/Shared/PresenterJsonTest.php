<?php

namespace Weather\Tests\Application\Shared;

use Weather\Application\Share\PresenterJson;
use PHPUnit\Framework\TestCase;
use stdClass;

class PresenterJsonTest extends TestCase
{
    public function testReadWrite(): void
    {
        $presenter = new PresenterJson();
        $response = new ResponseFake("Bonjour");
        $presenter->write($response);

        /** @var string $value */
        $value = $presenter->read();

        /** @var stdClass $decoded */
        $decoded = json_decode($value);
        $this->assertEquals("Bonjour", $decoded->myResponse);
    }
}
