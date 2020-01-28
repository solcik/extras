<?php

declare(strict_types=1);

namespace Solcik\Brick\DateTime;

use Brick\DateTime\ZonedDateTime;
use Nette\StaticClass;

final class ZonedDateTimeFactory
{
    use StaticClass;

    public static function now(): ZonedDateTime
    {
        return ZonedDateTime::now(TimeZoneFactory::create());
    }
}
