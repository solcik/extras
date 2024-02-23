<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\DateTime\TimeZone;
use Brick\DateTime\ZonedDateTime;
use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;

use function Safe\preg_replace;

final class ZonedDateTimeType extends Type
{
    /**
     * @var string
     */
    public const NAME = 'brick_zoneddatetime';

    public const PRECISION = 6;

    public const FORMAT = 'Y-m-d H:i:s.u e';

    public static ?string $timezone = null;

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     *
     * @return bool
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     *
     * @return string
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $type = $platform->getDateTimeTzTypeDeclarationSQL($fieldDeclaration);

        $precision = $fieldDeclaration['precision'] ?? self::PRECISION;

        if ($precision === 0) {
            return $type;
        }

        if (strpos($type, '(') !== false) {
            return preg_replace('/\(\d+\)/', "({$precision})", $type);
        }

        [$before, $after] = explode(' ', "{$type} ");

        return trim("{$before}({$precision}) {$after}");
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     *
     * @param ZonedDateTime|mixed|null $value
     *
     * @return string|null
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return $value;
        }

        if ($value instanceof ZonedDateTime) {
            // return $value->toDateTimeImmutable()->format($platform->getDateTimeTzFormatString());
            return $value->toDateTimeImmutable()->format(self::FORMAT);
        }

        throw ConversionException::conversionFailedInvalidType(
            $value,
            $this->getName(),
            ['null', ZonedDateTime::class]
        );
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
     *
     * @param ZonedDateTime|string|mixed|null $value
     *
     * @return ZonedDateTime|null
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value instanceof ZonedDateTime) {
            return $value;
        }

        // $val = DateTimeImmutable::createFromFormat($platform->getDateTimeTzFormatString(), $value);
        $dateTime = DateTimeImmutable::createFromFormat(self::FORMAT, $value);

        if ($dateTime === false) {
            $dateTime = date_create_immutable($value);
        }

        if ($dateTime === false) {
            throw ConversionException::conversionFailedFormat($value, $this->getName(), self::FORMAT);
        }

        $zdt = ZonedDateTime::fromDateTime($dateTime);

        if (self::$timezone === null) {
            return $zdt;
        }

        return $zdt->withTimeZoneSameInstant(TimeZone::parse(self::$timezone));
    }
}
