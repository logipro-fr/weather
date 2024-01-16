<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Tools;

use Weather\WeatherStack\Infrastructure\Tools\SplitQuery;
use PHPUnit\Framework\TestCase;

class SplitQueryTest extends TestCase
{
    public function testOnePointQueryMustNotBeSplited(): void
    {
        $query = "1.2345,123.1234";

        $splited = (new SplitQuery())->split($query);

        $this->assertEquals(1, count($splited));
        $this->assertEquals([$query], $splited);
    }

    public function testQuerySplitedBecauseTooLong(): void
    {
        $query = "1.2345,123.1234;2.2345,222.1234;3.2345,333.1234";

        $maxLengthOfQuery = 32;

        $splited = (new SplitQuery($maxLengthOfQuery))->split($query);

        $this->assertEquals(2, count($splited));
        $this->assertEquals("1.2345,123.1234;2.2345,222.1234", $splited[0]);
        $this->assertEquals("3.2345,333.1234", $splited[1]);
    }
}
