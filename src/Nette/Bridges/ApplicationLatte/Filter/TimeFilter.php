<?php

declare(strict_types=1);

namespace Solcik\Nette\Bridges\ApplicationLatte\Filter;

use Brick\DateTime\ZonedDateTime;
use Brick\Math\BigNumber;
use Nette\StaticClass;
use Solcik\Brick\DateTime\TimeZoneFactory;
use Solcik\Intl\Formatter;

final class TimeFilter
{
    use StaticClass;

    public static function timeS(int $seconds): string
    {
        return Formatter::timeS($seconds);
    }

    public static function timeMS(int $seconds): string
    {
        return Formatter::timeMS($seconds);
    }

    public static function timeHMS(int $seconds): string
    {
        return Formatter::timeHMS($seconds);
    }

    public static function secondsToHours(int $seconds): BigNumber
    {
        return Formatter::secondsToHours($seconds);
    }

    public static function zdtFormat(ZonedDateTime $dateTime, string $format): string
    {
        $dateTime = $dateTime->withTimeZoneSameInstant(TimeZoneFactory::create());

        return $dateTime->toDateTimeImmutable()->format($format);
    }
}
