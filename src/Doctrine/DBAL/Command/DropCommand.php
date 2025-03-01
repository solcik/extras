<?php

declare(strict_types=1);

namespace Solcik\Doctrine\DBAL\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\SQLiteSchemaManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function is_string;

final class DropCommand extends DoctrineCommand
{
    public const int RETURN_CODE_NOT_DROP = 1;

    public const int RETURN_CODE_NO_FORCE = 2;

    public const int RETURN_CODE_DOES_NOT_EXIST = 3;

    protected static string $defaultName = 'dbal:database:drop';

    protected function configure(): void
    {
        $this->setName(self::$defaultName);
        $this->setDescription('Drops the configured database');

        $this->addOption(
            'connection',
            'c',
            InputOption::VALUE_OPTIONAL,
            'The connection to use for this command'
        );
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
        $connectionName = $input->getOption('connection');
        if ($connectionName === null) {
            $connectionName = $this->getDoctrine()->getDefaultConnectionName();
        }

        $connection = $this->getDoctrineConnection($connectionName);

        $ifExists = (bool) $input->getOption('if-exists');

        $params = $connection->getParams();

        if (isset($params['primary'])) {
            $params = $params['primary'];
        }

        $name = $params['path'] ?? ($params['dbname'] ?? false);
        if ($name === false) {
            throw new \InvalidArgumentException(
                "Connection does not contain a 'path' or 'dbname' parameter and cannot be dropped."
            );
        }

        /* Need to be compatible with DBAL < 4, which still has `$params['url']` */
        /* @phpstan-ignore unset.offset */
        unset($params['dbname'], $params['url']);

        if ($connection->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            /* It's still available in DBAL 3.x that we need to support */
            /* @phpstan-ignore nullCoalesce.offset */
            $params['dbname'] = $params['default_dbname'] ?? 'postgres';
        }

        $force = (bool) $input->getOption('force');

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
        $connection = DriverManager::getConnection($params, $connection->getConfiguration());
        $schemaManager = $connection->createSchemaManager();
        $shouldDropDatabase = !$ifExists || in_array($name, $schemaManager->listDatabases(), true);

        // Only quote if we don't have a path
        if (!isset($params['path'])) {
            $name = $connection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }

        try {
            if ($shouldDropDatabase) {
                if ($schemaManager instanceof SQLiteSchemaManager) {
                    // dropDatabase() is deprecated for Sqlite
                    $connection->close();
                    if (file_exists($name)) {
                        unlink($name);
                    }
                } else {
                    $schemaManager->dropDatabase($name);
                }

                $output->writeln(
                    sprintf(
                        '<info>Dropped database <comment>%s</comment> for connection named <comment>%s</comment></info>',
                        $name,
                        $connectionName
                    )
                );
            } else {
                $output->writeln(
                    sprintf(
                        '<info>Database <comment>%s</comment> for connection named <comment>%s</comment> doesn\'t exist. Skipped.</info>',
                        $name,
                        $connectionName
                    )
                );
            }

            return 0;
        } catch (\Throwable $e) {
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
    }
}
