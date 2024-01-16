<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\Doctrine\Types;

use Weather\Share\Domain\LocationTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Safe\DateTimeImmutable;

class LocationTimeType extends Type
{
    public const TYPE_NAME = 'location_time';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param LocationTime            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->__toString();
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param string            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return LocationTime
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($latitude,$longitude,$datetime) = explode(" ", $value);
        /** @var DateTimeImmutable $when */
        $when = DateTimeImmutable::createFromFormat("Y-m-d/H:i:s", $datetime);
        return new LocationTime((float)$latitude, (float)$longitude, $when);
    }

    /**
     * Gets the SQL declaration snippet for a column of this type.
     *
     * @param mixed[]          $column   The column definition
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return string
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }
}
