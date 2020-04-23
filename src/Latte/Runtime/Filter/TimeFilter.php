<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Brick\Math\BigNumber;
use Nette\StaticClass;
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
}
