<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;

/**
 * Base class for Doctrine console commands to extend from.
 */
abstract class DoctrineCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    /**
     * Get a doctrine entity manager by symfony name.
     */
    protected function getEntityManager(string $name, ?int $shardId = null): EntityManagerInterface
    {
        $manager = $this->getDoctrine()->getManager($name);

        if ($shardId !== null) {
            throw new \InvalidArgumentException('Shards are not supported anymore using doctrine/dbal >= 3');
        }

        \assert($manager instanceof EntityManagerInterface);

        return $manager;
    }

    /**
     * Get a doctrine dbal connection by symfony name.
     */
    protected function getDoctrineConnection(string $name): Connection
    {
        return $this->getDoctrine()->getConnection($name);
    }

    protected function getDoctrine(): ManagerRegistry
    {
        return $this->doctrine;
    }
}
