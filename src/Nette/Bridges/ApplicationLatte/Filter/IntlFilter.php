<?php

declare(strict_types=1);

namespace Solcik\Nette\Bridges\ApplicationLatte\Filter;

use Brick\Math\BigNumber;
use Latte\Engine;
use Solcik\Intl\IntlFormatter;

final class IntlFilter implements Filter
{
    private IntlFormatter $intlFormatter;

    public function __construct(IntlFormatter $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }

    public function install(Engine $latte): void
    {
        $latte->addFilter(
            'percentage',
            fn (BigNumber $number, int $scale = 0): string => $this->intlFormatter->percentage($number, $scale)
        );

        $latte->addFilter(
            'number',
            fn ($number, int $scale = 0): string => $this->intlFormatter->number($number, $scale)
        );
    }
}
