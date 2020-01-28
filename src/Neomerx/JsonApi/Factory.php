<?php

declare(strict_types=1);

namespace Solcik\Neomerx\JsonApi;

use Neomerx\JsonApi\Contracts\Schema\SchemaContainerInterface;
use Neomerx\JsonApi\Factories\Factory as BaseFactory;

final class Factory extends BaseFactory
{
    public function createContainer(array $providers = []): SchemaContainerInterface
    {
        return new SchemaContainer($this, $providers);
    }
}
