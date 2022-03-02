<?php

declare(strict_types=1);

namespace Solcik\Tester;

use Nette\Configurator;
use Nette\DI\Container;

trait CompiledContainer
{
    protected ?Container $container = null;

    protected function getContainer(): Container
    {
        if ($this->container === null) {
            $this->container = $this->createContainer();
        }

        return $this->container;
    }

    abstract protected function createContainer(): Container;

    protected function isContainerCreated(): bool
    {
        return $this->container !== null;
    }

    protected function tearDownContainer(): void
    {
        if ($this->container) {
            $this->container = null;
        }
    }

    protected function createConfiguration(array $extensions = [], array $configs = []): Configurator
    {
        $configurator = new Configurator();
        $configurator->setTempDirectory(TEMP_DIR);

        $configurator->defaultExtensions = array_filter(
            $configurator->defaultExtensions,
            static function (string $key) use ($extensions) {
                return in_array($key, $extensions, true);
            },
            ARRAY_FILTER_USE_KEY
        );

        foreach ($configs as $file) {
            $configurator->addConfig($file);
        }

        return $configurator;
    }
}
