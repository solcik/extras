<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\DateTime\LocalTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

final class LocalTimeType extends Type
{
    public const string NAME = 'brick_localtime';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getTimeTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalTime) {
            return $value->toNativeDateTimeImmutable()->format($platform->getTimeFormatString());
        }

        throw InvalidType::new($value, self::NAME, [LocalTime::class]);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LocalTime
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalTime) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $dateTime = \DateTimeImmutable::createFromFormat('!' . $platform->getTimeFormatString(), $value);

        if ($dateTime === false) {
            throw InvalidFormat::new($value, self::NAME, $platform->getTimeFormatString());
        }

        return LocalTime::fromNativeDateTime($dateTime);
    }
}
