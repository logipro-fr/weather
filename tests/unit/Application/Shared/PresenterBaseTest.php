<?php

namespace Weather\Tests\Application\Shared;

use Weather\Application\Share\PresenterBase;
use PHPUnit\Framework\TestCase;

class PresenterBaseTest extends TestCase
{
    public function testReadWrite(): void
    {
        $presenter = new PresenterBase();
        $response = new ResponseFake("Bonjour");
        $presenter->write($response);

        $value = $presenter->read();

        $this->assertSame("Bonjour", $value['myResponse']);
    }
}
