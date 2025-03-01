<?php

declare(strict_types=1);

namespace Solcik\Test\Doctrine\DBAL\Type;

use Brick\DateTime\LocalDateTime;
use Brick\DateTime\TimeZone;
use Brick\DateTime\ZonedDateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Solcik\Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Solcik\Doctrine\DBAL\Type\ZonedDateTimeType;

final class ZonedDateTimeTest extends TestCase
{
    public const string ZONE = 'Europe/Prague';

    public function testDatabasePlatform(): void
    {
        $platform = new PostgreSQLPlatform();

        self::assertInstanceOf(PostgreSQLPlatform::class, $platform);
        self::assertSame('Y-m-d H:i:s.uO', $platform->getDateTimeTzFormatString());
    }

    public function testGetSQLDeclaration(): void
    {
        $platform = new PostgreSQLPlatform();
        $type = new ZonedDateTimeType();

        $converted = $type->getSQLDeclaration([], $platform);
        self::assertSame('TIMESTAMP(6) WITH TIME ZONE', $converted);
    }

    public function testConvertToDatabaseValueNull(): void
    {
        $platform = new PostgreSQLPlatform();
        $type = new ZonedDateTimeType();

        $converted = $type->convertToDatabaseValue(null, $platform);
        self::assertNull($converted);
    }

    #[DataProvider('provideToDatabase')]
    public function testConvertToDatabaseValue(string $expected, ZonedDateTime $value): void
    {
        $platform = new PostgreSQLPlatform();
        $type = new ZonedDateTimeType();

        $converted = $type->convertToDatabaseValue($value, $platform);
        self::assertSame($expected, $converted);
    }

    public static function provideToDatabase(): array
    {
        return [
            [
                '2020-07-19 06:56:50.123456+0200',
                ZonedDateTime::of(
                    LocalDateTime::of(2020, 07, 19, 6, 56, 50, 123456789),
                    TimeZone::parse('Europe/Prague')
                ),
            ],
            [
                '2020-07-19 06:56:50.123456+0000',
                ZonedDateTime::of(
                    LocalDateTime::of(2020, 07, 19, 6, 56, 50, 123456789),
                    TimeZone::parse('Z')
                ),
            ],
            [
                '2020-07-19 06:56:50.123456+0200',
                ZonedDateTime::of(
                    LocalDateTime::of(2020, 07, 19, 6, 56, 50, 123456789),
                    TimeZone::parse('+02:00')
                ),
            ],
        ];
    }

    public function testConvertToPHPValueNull(): void
    {
        $platform = new PostgreSQLPlatform();
        $type = new ZonedDateTimeType();

        $converted = $type->convertToPHPValue(null, $platform);
        self::assertNull($converted);
    }

    #[DataProvider('provideToPHP')]
    public function testConvertToPHPValue(string $expected, string $value, ?string $timeZone = null): void
    {
        $platform = new PostgreSQLPlatform();
        $type = new ZonedDateTimeType();
        $type::$timezone = $timeZone;

        $converted = $type->convertToPHPValue($value, $platform);
        self::assertInstanceOf(ZonedDateTime::class, $converted);
        self::assertSame($expected, $converted->__toString());
    }

    public static function provideToPHP(): array
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
