<?php

namespace Weather\Infrastructure\Shared\Tools;

use DateTimeZone;
use Safe\DateTimeImmutable;
use Weather\Domain\Model\Exceptions\InvalidArgumentException;
use Weather\Domain\Model\Weather\Point;

class ArgumentParser
{
    private const INVALID_ARGUMENT_CODE = 400;

    private const POINT_DELIMITER       = ";";
    private const VALUE_DELIMITER       = ",";

    private const MULTIPLE_POINTS_REGEX = '/^\d+(\.\d*)?,\d+(\.\d*)?(;\d+(\.\d*)?,\d+(\.\d*)?)*$/';
    private const SINGLE_POINTS_REGEX   = '/^\d+(\.\d*)?,\d+(\.\d*)?$/';
    private const DATE_REGEX            = '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}(:\d{2}(\.\d{1,6})?)?$/';

    /**
     * @return array<Point>
     */
    public function extractPoints(string $pointsString): array
    {
        if (!preg_match(self::MULTIPLE_POINTS_REGEX, $pointsString)) {
            throw new InvalidArgumentException(
                "point format invalid, should look like \"45.043,3.883;48.867,2.333\"",
                self::INVALID_ARGUMENT_CODE
            );
        }
        return $this->extractPointsInternal($pointsString);
    }

    /**
     * @return array<Point>
     */
    private function extractPointsInternal(string $pointsString): array
    {
        $res = [];
        foreach (explode(self::POINT_DELIMITER, $pointsString) as $point) {
            array_push($res, $this->stringToPointInternal($point));
        }
        return $res;
    }

    public function stringToPoint(string $value): Point
    {
        if (!preg_match(self::SINGLE_POINTS_REGEX, $value)) {
            throw new InvalidArgumentException(
                "point format invalid, should look like \"45.043,3.883\"",
                self::INVALID_ARGUMENT_CODE
            );
        }
        return $this->stringToPointInternal($value);
    }

    private function stringToPointInternal(string $value): Point
    {
        $value = explode(self::VALUE_DELIMITER, $value);
        return new Point(floatval($value[0]), floatval($value[1]));
    }

    public function extractDate(string $dateString): DateTimeImmutable
    {

        if (!(preg_match(self::DATE_REGEX, $dateString))) {
            throw new InvalidArgumentException(
                "date format invalid, should look like \"YYYY-MM-DD hh:mm:ss\"",
                self::INVALID_ARGUMENT_CODE
            );
        }
        return $this->extractDateInternal($dateString);
    }

    private function extractDateInternal(string $dateString): DateTimeImmutable
    {
        $format = "";
        switch (strlen($dateString)) {
            case 16:
                $format = "Y-m-d h:i";
                break;
            case 19:
                $format = "Y-m-d h:i:s";
                break;
            default:
                $format = "Y-m-d h:i:s.u";
                break;
        }

        return DateTimeImmutable::createFromFormat(
            $format,
            $dateString,
            new DateTimeZone(date_default_timezone_get())
        );
    }
}
