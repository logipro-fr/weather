<?php

namespace Weather\Infrastructure\Persistence\Doctrine\Types;

use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Safe\DateTimeImmutable;

class SafeDateTimeImmutableType extends Type
{
    public const FORMAT = "Y-m-d H:i:s.u";

    public function getName(): string
    {
        return "safe_datetime_immutable";
    }

    /**
     * @param DateTimeImmutable $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value->format(self::FORMAT);
    }

    /**
     * @param string $value
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTimeImmutable
    {
        return DateTimeImmutable::createFromFormat(
            self::FORMAT,
            $value,
            new DateTimeZone(date_default_timezone_get())
        );
    }

    /**
     * @param mixed[] $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return Types::DATETIME_MUTABLE . "(6)";
    }
}
