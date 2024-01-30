<?php

namespace Weather\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Weather\Domain\Model\Weather\Point;

use function SafePHP\floatval;

class PointType extends Type
{
    public function getName(): string
    {
        return "point";
    }

    /**
     * @param Point $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value->__toString();
    }

    /**
     * @param string $value
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): Point
    {
        $values = explode(",", $value);
        return new Point(floatval($values[0]), floatval($values[1]));
    }

    /**
     * @param mixed[] $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return Types::TEXT;
    }
}
