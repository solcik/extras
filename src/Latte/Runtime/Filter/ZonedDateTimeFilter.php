<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Brick\DateTime\ZonedDateTime;

final class ZonedDateTimeFilter
{
    public function __invoke(ZonedDateTime $dateTime, string $format): string
    {
        return $dateTime->toDateTimeImmutable()->format($format);
    }
}
