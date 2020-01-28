<?php

declare(strict_types=1);

namespace Solcik\Nette\Bridges\ApplicationLatte\Filter;

use Latte\Engine;

interface Filter
{
    public function install(Engine $latte): void;
}
