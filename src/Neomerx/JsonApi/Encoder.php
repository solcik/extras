<?php

declare(strict_types=1);

namespace Solcik\Neomerx\JsonApi;

use Neomerx\JsonApi\Contracts\Factories\FactoryInterface;
use Neomerx\JsonApi\Encoder\Encoder as BaseEncoder;

final class Encoder extends BaseEncoder
{
    protected function getFactory(): FactoryInterface
    {
        return new Factory();
    }
}
