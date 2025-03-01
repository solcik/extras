<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\DateTime\LocalDate;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use Override;

final class LocalDateType extends Type
{
    public const string NAME = 'brick_localdate';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDateTypeDeclarationSQL($column);
    }

    /**
     * @param LocalDate|mixed|null $value
     *
     * @throws ConversionException
     */
    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDate) {
            return $value->toNativeDateTimeImmutable()->format($platform->getDateFormatString());
        }

        throw InvalidType::new($value, self::NAME, [LocalDate::class]);
    }

    /**
     * @param LocalDate|string|mixed|null $value
     *
     * @throws ConversionException
     */
    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?LocalDate
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof LocalDate) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $dateTime = DateTimeImmutable::createFromFormat('!' . $platform->getDateFormatString(), $value);

        if ($dateTime === false) {
            throw InvalidFormat::new($value, self::NAME, $platform->getDateFormatString());
        }

        return LocalDate::fromNativeDateTime($dateTime);
    }
}
