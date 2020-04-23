<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Brick\DateTime\ZonedDateTime;
use Solcik\Brick\DateTime\TimeZoneFactory;

final class ZonedDateTimeFilter
{
    public function __invoke(ZonedDateTime $dateTime, string $format): string
    {
        $dateTime = $dateTime->withTimeZoneSameInstant(TimeZoneFactory::create());

        return $dateTime->toDateTimeImmutable()->format($format);
    }
}
