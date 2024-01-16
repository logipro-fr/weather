<?php

namespace Weather\Tests\Infrastructure\PredicitiveModel;

use Weather\Infrastructure\PredictiveModel\PredictiveModelTools;
use PHPUnit\Framework\TestCase;

use function Safe\file_put_contents;

class PredictiveModelToolsTest extends TestCase
{
    public function testConvertOneHotPoints2String(): void
    {
        $onJsonHotpoint = <<<EOS
        [
            {
              "on_Latitude": 49.003,
              "on_Longitude": 2.537,
              "occurence": 35
            }
        ]
        EOS;
        $this->assertEquals("49.003,2.537", PredictiveModelTools::convertJsonHotPoints2String($onJsonHotpoint));
    }

    public function testConvertTwoHotPoints2String(): void
    {
        $jsonTwoHotPoints = (string)file_get_contents(__DIR__ . '/resources/list-of-2-hotpoints.json');

        $this->assertEquals(
            "49.003,2.537;43.121,5.953",
            PredictiveModelTools::convertJsonHotPoints2String($jsonTwoHotPoints)
        );
    }
}
