<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Brick\Math\BigNumber;
use Solcik\Intl\IntlFormatter;

final readonly class NumberFilter
{
    public function __construct(
        private IntlFormatter $intlFormatter,
    ) {
    }

    public function __invoke(BigNumber|int|float $number, int $scale = 0): string
    {
        return $this->intlFormatter->number($number, $scale);
    }
}
