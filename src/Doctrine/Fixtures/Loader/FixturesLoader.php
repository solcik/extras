<?php

declare(strict_types=1);

namespace Solcik\Doctrine\Fixtures\Loader;

use Doctrine\Common\DataFixtures\Loader;
use Nette\DI\Container;
use Nette\DI\MissingServiceException;

class FixturesLoader extends Loader
{
    private Container $container;

    /** @var string[] */
    private array $paths;


    /**
     * @param string[] $paths
     */
    public function __construct(array $paths, Container $container)
    {
        $this->paths = $paths;
        $this->container = $container;
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


    /**
     * @param string $class
     */
    protected function createFixture($class)
    {
        try {
            return $this->container->getByType($class);
        } catch (MissingServiceException $e) {
            return parent::createFixture($class);
        }
    }
}
