<?php

declare(strict_types=1);

namespace Solcik\Neomerx\JsonApi;

use Doctrine\Common\Util\ClassUtils;
use Neomerx\JsonApi\Contracts\Schema\SchemaInterface;
use Neomerx\JsonApi\Schema\SchemaContainer as BaseSchemaContainer;
use Override;

final class SchemaContainer extends BaseSchemaContainer
{
    public function registerProvider(SchemaInterface $provider): void
    {
        $this->register($provider->getType(), $provider);
    }

    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     *
     * @param object $resource
     */
    #[Override]
    protected function getResourceType($resource): string
    {
        return ClassUtils::getRealClass($resource::class);
    }
}
