<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\DateTime\TimeZone;
use Brick\DateTime\ZonedDateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use Override;
use Safe\Exceptions\DatetimeException;

use function assert;
use function is_string;
use function Safe\date_create_immutable;
use function Safe\preg_replace;

final class ZonedDateTimeType extends Type
{
    public const string NAME = 'brick_zoneddatetime';

    public static int $precision = 6;

    public static ?string $timezone = null;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $type = $platform->getDateTimeTzTypeDeclarationSQL($column);

        /** @var int $precision */
        $precision = $column['precision'] ?? self::$precision;

        if ($precision === 0) {
            return $type;
        }

        if (str_contains($type, '(')) {
            $replaced = preg_replace('/\(\d+\)/', "({$precision})", $type);

            assert(is_string($replaced));

            return $replaced;
        }

        [$before, $after] = explode(' ', "{$type} ");

        return trim("{$before}({$precision}) {$after}");
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof ZonedDateTime) {
            return $value->toNativeDateTimeImmutable()->format($platform->getDateTimeTzFormatString());
        }

        throw InvalidType::new($value, self::NAME, [ZonedDateTime::class]);
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?ZonedDateTime
    {
        if ($value instanceof ZonedDateTime) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $dateTime = DateTimeImmutable::createFromFormat($platform->getDateTimeTzFormatString(), $value);

        if ($dateTime === false) {
            try {
                $dateTime = date_create_immutable($value);
            } catch (DatetimeException) {
                throw InvalidFormat::new($value, self::NAME, null);
            }
        }

        $zdt = ZonedDateTime::fromNativeDateTime($dateTime);

        if (self::$timezone === null) {
            return $zdt;
        }

        return $zdt->withTimeZoneSameInstant(TimeZone::parse(self::$timezone));
    }
}
