<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\Math\BigInteger;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;
use Override;

final class BigIntegerType extends Type
{
    public const string NAME = 'brick_integer';

    public static int $precision = 10;

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['precision'] ??= self::$precision;
        $column['scale'] = 0;

        return $platform->getDecimalTypeDeclarationSQL($column);
    }

    #[Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?BigInteger
    {
        if ($value === null) {
            return null;
        }

        if (!is_float($value) && !is_int($value) && !is_string($value)) {
            throw InvalidType::new($value, self::NAME, ['float', 'int', 'string']);
        }

        return BigInteger::of($value);
    }

    #[Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BigInteger) {
            return (string) $value;
        }

        throw InvalidType::new($value, self::NAME, [BigInteger::class]);
    }
}
