<?php

declare(strict_types=1);

namespace Solcik\WebpackLoader\DI;

use Nette\DI\CompilerExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Override;
use Solcik\WebpackLoader\ControlFactory;
use stdClass;

final class WebpackLoaderExtension extends CompilerExtension
{
    #[Override]
    public function getConfigSchema(): Schema
    {
        $builder = $this->getContainerBuilder();

        $wwwDir = $builder->parameters['wwwDir'];

        assert(is_string($wwwDir));

        return Expect::structure(
            [
                'stats' => Expect::string($wwwDir . '/dist/asset-manifest.json'),
                'wwwDir' => Expect::string($wwwDir),
            ]
        );
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        /** @var stdClass $config */
        $config = $this->getConfig();

        $builder
            ->addDefinition($this->prefix('controlFactory'))
            ->setType(ControlFactory::class)
            ->setFactory(ControlFactory::class, [$config->stats, $config->wwwDir]);
    }
}
