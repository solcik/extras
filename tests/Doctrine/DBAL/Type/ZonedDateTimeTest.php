<?php

declare(strict_types=1);

namespace Solcik\Test\Doctrine\DBAL\Type;

use Brick\DateTime\LocalDateTime;
use Brick\DateTime\TimeZone;
use Brick\DateTime\ZonedDateTime;
use Doctrine\DBAL\Platforms\PostgreSQL100Platform;
use PHPUnit\Framework\TestCase;
use Solcik\Doctrine\DBAL\Type\ZonedDateTimeType;

final class ZonedDateTimeTest extends TestCase
{
    public const string ZONE = 'Europe/Prague';

    public function testDatabasePlatform(): void
    {
        $platform = new PostgreSQL100Platform();

        self::assertInstanceOf(PostgreSQL100Platform::class, $platform);
        self::assertSame('Y-m-d H:i:sO', $platform->getDateTimeTzFormatString());
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = new PostgreSQL100Platform();
        $type = new ZonedDateTimeType();

        $converted = $type->getSQLDeclaration([], $platform);
        self::assertSame('TIMESTAMP(6) WITH TIME ZONE', $converted);
    }

    public function testConvertToDatabaseValueNull(): void
    {
        $platform = new PostgreSQL100Platform();
        $type = new ZonedDateTimeType();

        $converted = $type->convertToDatabaseValue(null, $platform);
        self::assertNull($converted);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideToDatabase')]
    public function testConvertToDatabaseValue(string $expected, ZonedDateTime $value): void
    {
        $platform = new PostgreSQL100Platform();
        $type = new ZonedDateTimeType();

        $converted = $type->convertToDatabaseValue($value, $platform);
        self::assertSame($expected, $converted);
    }

    public function provideToDatabase(): array
    {
        return [
            [
                '2020-07-19 06:56:50.123456 Europe/Prague',
                ZonedDateTime::of(
                    LocalDateTime::of(2020, 07, 19, 6, 56, 50, 123456789),
                    TimeZone::parse('Europe/Prague')
                ),
            ],
            [
                '2020-07-19 06:56:50.123456 Z',
                ZonedDateTime::of(
                    LocalDateTime::of(2020, 07, 19, 6, 56, 50, 123456789),
                    TimeZone::parse('Z')
                ),
            ],
            [
                '2020-07-19 06:56:50.123456 +02:00',
                ZonedDateTime::of(
                    LocalDateTime::of(2020, 07, 19, 6, 56, 50, 123456789),
                    TimeZone::parse('+02:00')
                ),
            ],
        ];
    }

    public function testConvertToPHPValueNull(): void
    {
        $platform = new PostgreSQL100Platform();
        $type = new ZonedDateTimeType();

        $converted = $type->convertToPHPValue(null, $platform);
        self::assertNull($converted);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('provideToPHP')]
    public function testConvertToPHPValue(string $expected, string $value, ?string $timeZone = null): void
    {
        $platform = new PostgreSQL100Platform();
        $type = new ZonedDateTimeType();
        $type::$timezone = $timeZone;

        $converted = $type->convertToPHPValue($value, $platform);
        self::assertInstanceOf(ZonedDateTime::class, $converted);
        self::assertSame($expected, $converted->__toString());
    }

    public function provideToPHP(): array
    {
        return [
            ['2020-07-19T06:56:50+02:00[Europe/Prague]', '2020-07-19 06:56:50+02', self::ZONE],
            ['2020-07-19T06:56:50+02:00[Europe/Prague]', '2020-07-19 06:56:50+02:00', self::ZONE],
            ['2020-07-19T06:56:50+02:00[Europe/Prague]', '2020-07-19 06:56:50 +02', self::ZONE],
            ['2020-07-19T06:56:50+02:00[Europe/Prague]', '2020-07-19 06:56:50 +02:00', self::ZONE],
            ['2020-07-19T06:56:50.123456+02:00[Europe/Prague]', '2020-07-19 06:56:50.123456+02', self::ZONE],
            [
                '2020-07-19T06:56:50.123456+02:00[Europe/Prague]',
                '2020-07-19 06:56:50.123456+02:00',
                self::ZONE,
            ],
            ['2020-07-19T06:56:50.123456+02:00[Europe/Prague]', '2020-07-19 06:56:50.123456 +02', self::ZONE],
            [
                '2020-07-19T06:56:50.123456+02:00[Europe/Prague]',
                '2020-07-19 06:56:50.123456 +02:00',
                self::ZONE,
            ],
            [
                // Default TimeZone in PHP is set to Europe/Prague
                '2020-07-19T04:56:50Z',
                '2020-07-19 06:56:50',
                'Z',
            ],
            [
                // Default TimeZone in PHP is set to Europe/Prague
                '2020-07-19T06:56:50+02:00[Europe/Prague]',
                '2020-07-19 06:56:50',
                null,
            ],
            ['2020-07-19T06:56:50Z', '2020-07-19 06:56:50Z', 'Z'],
            ['2020-07-19T04:56:50Z', '2020-07-19 06:56:50+02', 'Z'],
            ['2020-07-19T06:56:50+01:00', '2020-07-19 06:56:50+01', ZonedDateTimeType::$timezone],
            ['2020-07-19T06:56:50+01:00', '2020-07-19 06:56:50+01', null],
        ];
    }
}
