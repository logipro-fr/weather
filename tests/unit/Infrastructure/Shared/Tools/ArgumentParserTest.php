<?php

namespace Weather\Tests\WeatherStack\Infrastructure\Shared\Tools;

use PHPUnit\Framework\TestCase;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Weather\Domain\Model\Weather\Point;
use Weather\Infrastructure\Shared\Tools\ArgumentParser;

class ArgumentParserTest extends TestCase
{
    private ArgumentParser $parser;

    public function setUp(): void
    {
        $this->parser = new ArgumentParser();
    }

    public function testExtractPoint(): void
    {
        $targets = [
            new Point(0, 0),
            new Point(1, 1),
            new Point(3, 3)
        ];
        $pointsString = implode(";", $targets);
        $point = $this->parser->extractPoints($pointsString);

        $this->assertEquals($targets, $point);
    }

    public function testExtractPointFailsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883;48.867,2.333\"");
        $pointsString = "";
        $point = $this->parser->extractPoints($pointsString);
    }

    public function testExtractPointFailsTooManyValuesInOnePoint(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883;48.867,2.333\"");
        $pointsString = "1,2;3,4,5;6,7";
        $point = $this->parser->extractPoints($pointsString);
    }

    public function testExtractPointFailsTooManDdelimiters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883;48.867,2.333\"");
        $pointsString = "1,2;3,4;;5,6";
        $point = $this->parser->extractPoints($pointsString);
    }

    public function testExtractPointFailsInvalidCharacters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883;48.867,2.333\"");
        $pointsString = "1,2&3,4&5,6";
        $point = $this->parser->extractPoints($pointsString);
    }

    public function testStringToPoint(): void
    {
        $target = new Point(0, 0);
        $point = $this->parser->stringToPoint($target->__toString());

        $this->assertEquals($target, $point);
    }

    public function testStringToPointFailMissingValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883\"");
        $pointsString = "1,";
        $point = $this->parser->stringToPoint($pointsString);
    }

    public function testStringToPointFailMissingDelimiter(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883\"");
        $pointsString = "12";
        $point = $this->parser->stringToPoint($pointsString);
    }

    public function testStringToPointFailTooManyValues(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883\"");
        $pointsString = "1,2,3";
        $point = $this->parser->stringToPoint($pointsString);
    }

    public function testStringToPointFailInvalidCharacters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("point format invalid, should look like \"45.043,3.883\"");
        $pointsString = "I-2";
        $point = $this->parser->stringToPoint($pointsString);
    }

    public function testExtractDateMinute(): void
    {
        $target = DateTimeImmutable::createFromFormat("Y-m-d H:i", "2024-01-01 12:05");
        $date = $this->parser->extractDate($target->format("Y-m-d H:i"));

        $this->assertEquals($target, $date);
    }

    public function testExtractDateSecond(): void
    {
        $target = DateTimeImmutable::createFromFormat("Y-m-d H:i:s", "2024-01-01 12:05:03");
        $date = $this->parser->extractDate($target->format("Y-m-d H:i:s"));

        $this->assertEquals($target, $date);
    }

    public function testExtractDateTenthOfASecond(): void
    {
        $target = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-01 12:05:03.1");
        $date = $this->parser->extractDate($target->format("2024-01-01 12:05:03.1"));

        $this->assertEquals($target, $date);
    }

    public function testExtractDateMicrosecond(): void
    {
        $target = DateTimeImmutable::createFromFormat("Y-m-d H:i:s.u", "2024-01-01 12:05:03.123456");
        $date = $this->parser->extractDate($target->format("Y-m-d H:i:s.u"));

        $this->assertEquals($target, $date);
    }

    public function testExtractDateFail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(400);
        $this->expectExceptionMessage("date format invalid, should look like \"YYYY-MM-DD hh:mm:ss\"");
        $dateString = "01/01/2024 12:00";
        $date = $this->parser->extractDate($dateString);
    }
}
