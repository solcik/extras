<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\Math\BigDecimal;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use Override;

final class BigDecimalType extends Type
{
    public const string NAME = 'brick_decimal';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDecimalTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BigDecimal
    {
        if ($value === null) {
            return null;
        }

        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            throw InvalidType::new($value, self::NAME, ['float', 'int', 'string']);
        }

        return BigDecimal::of($value);
    }

    #[Override]
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BigDecimal) {
            return (string) $value;
        }

        throw InvalidType::new($value, self::NAME, ['null', BigDecimal::class]);
    }
}
