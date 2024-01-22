<?php

namespace Weather\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Safe\DateTimeImmutable;

use function SafePHP\floatval;

class SafeDateTimeImmutableType extends Type{
    public function getName(): string
    {
        return "datetime_immutable";
    }

    /**
     * @param DateTimeImmutable $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value->format("Y-m-d H:i:s");
    }

    /**
     * @param string $value
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): DateTimeImmutable
    {
        return new DateTimeImmutable($value);
    }

    /**
     * @param mixed[] $column
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return Types::STRING;
    }
}