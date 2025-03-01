<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Brick\DateTime\LocalDate;

final class LocalDateFilter
{
    public function __invoke(LocalDate $date, string $format): string
    {
        return $date->toNativeDateTimeImmutable()->format($format);
    }
}
