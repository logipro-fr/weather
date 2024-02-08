<?php

namespace Weather\Tests\Domain\Model\Weather;

use PHPUnit\Framework\TestCase;
use Weather\Domain\Model\Weather\Source;

use function Safe\json_encode;

class SourceTest extends TestCase
{
    public function testSource(): void
    {
        $source = Source::DEBUG;

        $this->assertEquals("debug", $source->getName());
        $this->assertEquals("http://example.com/", $source->getUrl());
    }

    public function testSerialise(): void
    {
        $source = Source::DEBUG;

        $this->assertEquals('{"name":"debug","url":"http:\/\/example.com\/"}', json_encode($source));
    }
}
