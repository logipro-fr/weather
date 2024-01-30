<?php

namespace Weather\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Weather\Domain\Model\Weather\WeatherInfoId;

class WeatherInfoIdType extends Type
{
    public function getName(): string
    {
        return "weather_info_id";
    }

    /**
     * @param WeatherInfoId $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value->getId();
    }

    /**
     * @param string $value
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): WeatherInfoId
    {
        return new WeatherInfoId($value);
    }

    /**
     * @param mixed[] $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'varchar(40)';
    }
}
