<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\Persistence\ManagerRegistry;
use Solcik\Exception\Logic\DBALConnectionArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateCommand extends Command
{
    /**
     * @var string string
     */
    protected static $defaultName = 'dbal:database:create';

    private ManagerRegistry $managerRegistry;


    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct();

        $this->managerRegistry = $managerRegistry;
    }


    protected function configure(): void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Creates the configured database');

        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'The connection to use for this command');
        $this->addOption(
            'if-not-exists',
            null,
            InputOption::VALUE_NONE,
            'Don\'t trigger an error, when the database already exists'
        );

        $this->setHelp(
            <<<EOT
The <info>%command.name%</info> command creates the default connections database:
    <info>php %command.full_name%</info>
You can also optionally specify the name of a connection to create the database for:
    <info>php %command.full_name% --connection=default</info>
EOT
        );
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $connectionName */
        $connectionName = $input->getOption('connection');
        $connectionName = (string) $connectionName;
        $dontScreamIfExists = (bool) $input->getOption('if-not-exists');

        if ($connectionName === '') {
            $connectionName = $this->managerRegistry->getDefaultConnectionName();
        }
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection($connectionName);

        $params = $connection->getParams();

        if (isset($params['master'])) {
            $params = $params['master'];
        }

        $name = $params['path'] ?? $params['dbname'] ?? null;

        if ($name === null) {
            throw new DBALConnectionArgumentException(
                "Connection does not contain a 'path' or 'dbname' parameter and cannot be created."
            );
        }
        unset($params['dbname'], $params['path'], $params['url']);

        // We drop previous connection and create a new one without path/dbname.
        $connection = DriverManager::getConnection($params);
        $connection->connect();
        $tmpSchemaManager = $connection->getSchemaManager();
        $dbExists = in_array($name, $tmpSchemaManager->listDatabases(), true);

        // Only quote if we don't have a path
        if (!isset($params['path'])) {
            $databasePlatform = $connection->getDatabasePlatform();
            $name = $databasePlatform->quoteSingleIdentifier($name);
        }

        if ($dbExists && $dontScreamIfExists) {
            $output->writeln(
                sprintf(
                    '<info>Database <comment>%s</comment> for connection named <comment>%s</comment> already exists. Skipped.</info>',
                    $name,
                    $connectionName
                )
            );
            $connection->close();

            return 0;
        }

        try {
            $tmpSchemaManager->createDatabase($name);
        } catch (DBALException $e) {
            $output->writeln(
                sprintf(
                    '<error>Could not create database <comment>%s</comment> for connection named <comment>%s</comment></error>',
                    $name,
                    $connectionName
                )
            );
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return 1;
        }

        $output->writeln(
            sprintf(
                '<info>Created database <comment>%s</comment> for connection named <comment>%s</comment></info>',
                $name,
                $connectionName
            )
        );
        $connection->close();

        return 0;
    }
}
