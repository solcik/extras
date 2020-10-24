<?php

declare(strict_types=1);

namespace Solcik\Latte\Runtime\Filter;

use function Safe\json_encode;

final class JsonEncodeFilter
{
    /**
     * @param array|object $data
     */
    public function __invoke($data): string
    {
        return json_encode($data);
    }
}
