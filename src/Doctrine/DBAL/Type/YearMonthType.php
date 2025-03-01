<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Type;

use Brick\DateTime\DateTimeException;
use Brick\DateTime\Parser\IsoParsers;
use Brick\DateTime\YearMonth;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\StringType;

final class YearMonthType extends StringType
{
    public const string NAME = 'brick_yearmonth';

    #[\Override]
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        $column['length'] = 7;

        return $platform->getStringTypeDeclarationSQL($column);
    }

    #[\Override]
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof YearMonth) {
            return $value->__toString();
        }

        throw InvalidType::new($value, self::NAME, [YearMonth::class]);
    }

    #[\Override]
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?YearMonth
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof YearMonth) {
            return $value;
        }

        if (!is_string($value)) {
            return null;
        }

        $parser = IsoParsers::yearMonth();

        try {
            return YearMonth::parse($value, $parser);
        } catch (DateTimeException) {
            throw InvalidFormat::new($value, self::NAME, $parser->getPattern());
        }
    }
}
