<?php

namespace Weather\WeatherStack\Infrastructure\Persistence\Doctrine\Types;

use Weather\WeatherStack\Domain\Model\CurrentWeather\CurrentWeatherId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

class CurrentWeatherIdType extends Type
{
    public const TYPE_NAME = 'current_weather_id';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param CurrentWeatherId            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return mixed The database representation of the value.
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getId();
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param string            $value    The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     *
     * @return CurrentWeatherId
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new CurrentWeatherId($value);
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
