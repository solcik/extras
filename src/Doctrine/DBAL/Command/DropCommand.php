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

final class DropCommand extends Command
{
    /**
     * @var int
     */
    public const RETURN_CODE_NOT_DROP = 1;

    /**
     * @var int
     */
    public const RETURN_CODE_NO_FORCE = 2;

    /**
     * @var int
     */
    public const RETURN_CODE_DOES_NOT_EXIST = 3;

    /**
     * @var string string
     */
    protected static $defaultName = 'dbal:database:drop';

    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct();

        $this->managerRegistry = $managerRegistry;
    }

    protected function configure(): void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Drops the configured database');

        $this->addOption('connection', 'c', InputOption::VALUE_OPTIONAL, 'The connection to use for this command');
        $this->addOption(
            'if-exists',
            null,
            InputOption::VALUE_NONE,
            'Don\'t trigger an error, when the database doesn\'t exist'
        );
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Set this parameter to execute this action');

        $this->setHelp(
            <<<EOT
The <info>%command.name%</info> command drops the default connections database:
    <info>php %command.full_name%</info>
The <info>--force</info> parameter has to be used to actually drop the database.
You can also optionally specify the name of a connection to drop the database for:
    <info>php %command.full_name% --connection=default</info>
<error>Be careful: All data in a given database will be lost when executing this command.</error>
EOT
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string|null $connectionName */
        $connectionName = $input->getOption('connection');
        $doNotScreamIfExists = (bool) $input->getOption('if-exists');
        $force = (bool) $input->getOption('force');

        if ($connectionName === '' || $connectionName === null) {
            $connectionName = $this->managerRegistry->getDefaultConnectionName();
        }

        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection($connectionName);

        $driverOptions = [];
        $params = $connection->getParams();

        if (isset($params['driverOptions'])) {
            $driverOptions = $params['driverOptions'];
        }

        // Since doctrine/dbal 2.11 master has been replaced by primary
        if (isset($params['primary'])) {
            $params = $params['primary'];
            $params['driverOptions'] = $driverOptions;
        }

        if (isset($params['master'])) {
            $params = $params['master'];
            $params['driverOptions'] = $driverOptions;
        }

        $name = $params['path'] ?? $params['dbname'] ?? null;

        if ($name === null) {
            throw new DBALConnectionArgumentException(
                "Connection does not contain a 'path' or 'dbname' parameter and cannot be dropped."
            );
        }
        unset($params['dbname'], $params['url']);

        if (!$force) {
            $output->writeln(
                '<error>ATTENTION:</error> This operation should not be executed in a production environment.'
            );
            $output->writeln('');
            $output->writeln(
                sprintf(
                    '<info>Would drop the database <comment>%s</comment> for connection named <comment>%s</comment>.</info>',
                    $name,
                    $connectionName
                )
            );
            $output->writeln('Please run the operation with --force to execute');
            $output->writeln('<error>All data will be lost!</error>');

            return self::RETURN_CODE_NO_FORCE;
        }

        // Reopen connection without database name set
        // as some vendors do not allow dropping the database connected to.
        $connection->close();
        $connection = DriverManager::getConnection($params);
        $schemaManager = $connection->getSchemaManager();
        $dbExists = in_array($name, $schemaManager->listDatabases(), true);

        // Only quote if we don't have a path
        if (!isset($params['path'])) {
            $databasePlatform = $connection->getDatabasePlatform();
            $name = $databasePlatform->quoteSingleIdentifier($name);
        }

        if (!$dbExists) {
            if ($doNotScreamIfExists) {
                return 0;
            }

            $output->writeln(
                sprintf(
                    '<info>Database <comment>%s</comment> for connection named <comment>%s</comment> doesn\'t exist. Skipped.</info>',
                    $name,
                    $connectionName
                )
            );

            return self::RETURN_CODE_DOES_NOT_EXIST;
        }

        try {
            $schemaManager->dropDatabase($name);
        } catch (DBALException $e) {
            $output->writeln(
                sprintf(
                    '<error>Could not drop database <comment>%s</comment> for connection named <comment>%s</comment></error>',
                    $name,
                    $connectionName
                )
            );
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));

            return self::RETURN_CODE_NOT_DROP;
        }

        $output->writeln(
            sprintf(
                '<info>Dropped database <comment>%s</comment> for connection named <comment>%s</comment></info>',
                $name,
                $connectionName
            )
        );

        return 0;
    }
}
