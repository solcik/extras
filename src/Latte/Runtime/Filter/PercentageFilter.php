<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Solcik\Intl\IntlFormatter;

final class PercentageFilter
{
    private IntlFormatter $intlFormatter;


    public function __construct(IntlFormatter $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }


    public function __invoke(BigDecimal $number, int $scale = 0): string
    {
        $number = $number
            ->withPointMovedRight(2)
            ->dividedBy(BigDecimal::one(), $scale, RoundingMode::CEILING);

        return $this->intlFormatter->percentage($number, $scale);
    }
}
