<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Command\Tools;

use Weather\WeatherStack\Infrastructure\Command\Tools\CsvParser;
use PHPUnit\Framework\TestCase;

class CsvParserTest extends TestCase
{
    public function testParser(): void
    {
        $csv = <<<EOS
        ;Num_Acc;jour;mois;an
        0;2,021E+11;30;11;2021
        1;2,021E+11;25;9;2021
        EOS;
        $lignes = CsvParser::parse($csv);

        $this->assertEquals(2, count($lignes));

        $expectedColNames = ["","Num_Acc","jour","mois","an"];
        $this->assertEquals($expectedColNames, array_keys($lignes[0]));
        $this->assertEquals(0, $lignes[0][""]);
        $this->assertEquals("2,021E+11", $lignes[0]["Num_Acc"]);
        $this->assertEquals("30", $lignes[0]["jour"]);
        $this->assertEquals("11", $lignes[0]["mois"]);
        $this->assertEquals("2021", $lignes[0]["an"]);

        $this->assertEquals(1, $lignes[1][""]);
        $this->assertEquals("2,021E+11", $lignes[1]["Num_Acc"]);
        $this->assertEquals("25", $lignes[1]["jour"]);
        $this->assertEquals("9", $lignes[1]["mois"]);
        $this->assertEquals("2021", $lignes[1]["an"]);
    }

    public function testBadLineException(): void
    {
        $this->expectException(\ValueError::class);
        $this->expectExceptionMessage(
            'array_combine(): Argument #1 ($keys) and argument #2 ($values) must have the same number of elements'
        );
        $csv = <<<EOS
        col1;col2
        123
        EOS;
        CsvParser::parse($csv);
    }

    public function testTrunquate(): void
    {
        $csv = <<<EOS
        col1;col2
        123,123
        2,234
        3,567
        EOS;

        $result = CsvParser::trunquate($csv, 0);
        $expected = <<<EOS
        col1;col2
        EOS;
        $this->assertEquals($expected, $result);

        $result = CsvParser::trunquate($csv, 2);
        $expected = <<<EOS
        col1;col2
        123,123
        2,234
        EOS;
        $this->assertEquals($expected, $result);

        $result = CsvParser::trunquate($csv, 20);
        $expected = <<<EOS
        col1;col2
        123,123
        2,234
        3,567
        EOS;
        $this->assertEquals($expected, $result);

        $result = CsvParser::trunquate($csv, 2, 1);
        $expected = <<<EOS
        col1;col2
        2,234
        3,567
        EOS;
        $this->assertEquals($expected, $result);

        $result = CsvParser::trunquate($csv, 2, 2);
        $expected = <<<EOS
        col1;col2
        3,567
        EOS;
        $this->assertEquals($expected, $result);

        $result = CsvParser::trunquate($csv, 2, 3);
        $expected = <<<EOS
        col1;col2
        EOS;
        $this->assertEquals($expected, $result);

        $result = CsvParser::trunquate($csv, 1, 4);
        $expected = <<<EOS
        col1;col2
        EOS;
        $this->assertEquals($expected, $result);
    }
}
