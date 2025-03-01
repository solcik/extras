<?php

declare(strict_types=1);

namespace Solcik\Doctrine\Fixtures\Loader;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;
use Override;

class FixturesLoader extends Loader
{
    /**
     * @param string[] $paths
     */
    public function __construct(
        private readonly array $paths,
        private readonly Container $container,
    ) {
    }

    /**
     * @param string[] $paths
     */
    public function loadPaths(array $paths): void
    {
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $this->loadFromDirectory($path);
            } elseif (is_file($path)) {
                $this->loadFromFile($path);
            }
        }
    }

    public function load(): void
    {
        $this->loadPaths($this->paths);
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    #[Override]
    protected function createFixture(string $class): FixtureInterface
    {
        /** @var class-string $classString */
        $classString = $class;
        try {
            $type = $this->container->getByType($classString);

            assert($type instanceof FixtureInterface);

            return $type;
        } catch (MissingServiceException) {
            return parent::createFixture($classString);
        }
    }
}
