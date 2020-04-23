<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use Solcik\Intl\IntlFormatter;

final class NumberFilter
{
    private IntlFormatter $intlFormatter;


    public function __construct(IntlFormatter $intlFormatter)
    {
        $this->intlFormatter = $intlFormatter;
    }


    public function __invoke($number, int $scale = 0): string
    {
        return $this->intlFormatter->number($number, $scale);
    }
}
