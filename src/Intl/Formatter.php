<?php

declare(strict_types=1);

namespace Solcik\Intl;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Nette\StaticClass;

final class Formatter
{
    use StaticClass;

    public static function timeS(int $seconds): string
    {
        return (string) $seconds;
    }

    public static function timeMS(int $seconds): string
    {
        $negative = $seconds < 0 ? '-' : '';
        $seconds = abs($seconds);
        $modSeconds = $seconds % 60;
        $modSecondsStr = str_pad((string) $modSeconds, 2, '0', STR_PAD_LEFT);
        $minutes = (int) floor($seconds / 60);

        return "{$negative}{$minutes}:{$modSecondsStr}";
    }

    public static function timeHMS(int $seconds): string
    {
        $negative = $seconds < 0 ? '-' : '';
        $seconds = abs($seconds);
        $modSeconds = $seconds % 60;
        $modSecondsStr = str_pad((string) $modSeconds, 2, '0', STR_PAD_LEFT);
        $minutes = (int) floor($seconds / 60);
        $modMinutes = $minutes % 60;
        $modMinutesStr = str_pad((string) $modMinutes, 2, '0', STR_PAD_LEFT);
        $hours = (int) floor($minutes / 60);

        return "{$negative}{$hours}:{$modMinutesStr}:{$modSecondsStr}";
    }

    public static function secondsToHours(int $seconds): BigDecimal
    {
        return BigDecimal::of($seconds)->dividedBy(3600, 2, RoundingMode::HALF_UP);
    }
}
