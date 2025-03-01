<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Solcik\WebpackLoader\ControlFactory;

final class WebpackLoaderExtension extends CompilerExtension
{
    #[\Override]
    public function getConfigSchema(): Schema
    {
        $builder = $this->getContainerBuilder();

        return Expect::structure(
            [
                'stats' => Expect::string($builder->parameters['wwwDir'] . '/dist/asset-manifest.json'),
                'wwwDir' => Expect::string($builder->parameters['wwwDir']),
            ]
        );
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        /** @var \stdClass $config */
        $config = $this->getConfig();

        $builder
            ->addDefinition($this->prefix('controlFactory'))
            ->setType(ControlFactory::class)
            ->setFactory(ControlFactory::class, [$config->stats, $config->wwwDir]);
    }
}
