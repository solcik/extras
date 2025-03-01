<?php

declare(strict_types=1);

namespace Solcik\Neomerx\JsonApi\DI;

use Neomerx\JsonApi\Contracts\Schema\SchemaInterface;
use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Override;
use Solcik\Neomerx\JsonApi\Encoder;
use Solcik\Neomerx\JsonApi\Factory;
use Solcik\Neomerx\JsonApi\SchemaContainer;
use stdClass;

final class JsonApiExtension extends CompilerExtension
{
    #[Override]
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'options' => Expect::int(JSON_PRETTY_PRINT),
            'url' => Expect::string(''),
        ]);
    }

    public function beforeCompile(): void
    {
        $builder = $this->getContainerBuilder();

        $factory = $builder
            ->addDefinition($this->prefix('factory'))
            ->setType(Factory::class);

        $container = $builder
            ->addDefinition($this->prefix('container'))
            ->setType(SchemaContainer::class)
            ->setArguments([$factory, []]);

        foreach ($builder->findByType(SchemaInterface::class) as $def) {
            $container->addSetup('registerProvider', [$def]);
        }

        /** @var stdClass $config */
        $config = $this->getConfig();

        $builder
            ->addDefinition($this->prefix('encoder'))
            ->setType(Encoder::class)
            ->addSetup('withUrlPrefix', [$config->url])
            ->addSetup('withEncodeOptions', [$config->options])
            ->setAutowired();
    }
}
