<?php

declare(strict_types=1);

namespace Solcik\Doctrine\Fixtures\DI;

use Nette\DI\CompilerExtension;
use Nette\DI\Extensions\InjectExtension;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use Solcik\Doctrine\Fixtures\Command\LoadDataFixturesCommand;
use Solcik\Doctrine\Fixtures\Loader\FixturesLoader;

/**
 * @property \stdClass $config
 */
class FixturesExtension extends CompilerExtension
{
    #[\Override]
    public function getConfigSchema(): Schema
    {
        return Expect::structure([
            'paths' => Expect::listOf('string'),
        ]);
    }

    public function loadConfiguration(): void
    {
        $builder = $this->getContainerBuilder();
        $config = $this->config;

        $builder->addDefinition($this->prefix('fixturesLoader'))
            ->setFactory(FixturesLoader::class, [$config->paths]);

        $builder->addDefinition($this->prefix('loadDataFixturesCommand'))
            ->setFactory(LoadDataFixturesCommand::class)
            ->addTag(InjectExtension::TAG_INJECT, true)
            ->addTag('console.command', 'doctrine:fixtures:load');
    }
}
