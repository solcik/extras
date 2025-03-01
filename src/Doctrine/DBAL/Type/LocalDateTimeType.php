<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\DateTime\LocalDateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use function is_string;

final class LocalDateTimeType extends Type
{
    public const string NAME = 'brick_localdatetime';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTimeTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDateTime) {
            return $value->toNativeDateTimeImmutable()->format($platform->getDateTimeFormatString());
        }

        throw InvalidType::new($value, self::NAME, [LocalDateTime::class]);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LocalDateTime
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDateTime) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeFormatString(), $value);

        if ($dateTime === false) {
            throw InvalidFormat::new($value, self::NAME, $platform->getDateTimeFormatString());
        }

        return LocalDateTime::fromNativeDateTime($dateTime);
    }
}
