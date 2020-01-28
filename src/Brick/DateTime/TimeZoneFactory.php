<?php

declare(strict_types=1);

namespace Solcik\Brick\DateTime;

use Brick\DateTime\TimeZone;
use Brick\DateTime\TimeZoneRegion;
use Nette\StaticClass;

final class TimeZoneFactory
{
    use StaticClass;

    public static function create(): TimeZone
    {
        return TimeZoneRegion::parse('Europe/Prague');
    }
}
