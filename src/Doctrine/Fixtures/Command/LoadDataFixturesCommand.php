<?php

declare(strict_types=1);

namespace Solcik\Doctrine\Fixtures\Command;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Solcik\Doctrine\Fixtures\Loader\FixturesLoader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use function assert;
use function is_bool;

class LoadDataFixturesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'doctrine:fixtures:load';

    public function __construct(
        private readonly FixturesLoader $loader,
        private readonly ManagerRegistry $managerRegistry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName(static::$defaultName)
            ->setDescription('Load data fixtures to your database.')
            ->addOption(
                'fixtures',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'The directory to load data fixtures from.'
            )
            ->addOption(
                'append',
                null,
                InputOption::VALUE_NONE,
                'Append the data fixtures instead of deleting all data from the database first.'
            )
            ->addOption(
                'em',
                null,
                InputOption::VALUE_REQUIRED,
                'The entity manager to use for this command.'
            )
            ->addOption(
                'purge-with-truncate',
                null,
                InputOption::VALUE_NONE,
                'Purge data by using a database-level TRUNCATE statement'
            )
            ->setHelp('
The <info>doctrine:fixtures:load</info> command loads data fixtures from your config:

  <info>doctrine:fixtures:load</info>

You can also optionally specify the path to fixtures with the <info>--fixtures</info> option:

  <info>doctrine:fixtures:load --fixtures=/path/to/fixtures1 --fixtures=/path/to/fixtures2</info>

If you want to append the fixtures instead of flushing the database first you can use the <info>--append</info> option:

  <info>doctrine:fixtures:load --append</info>

By default Doctrine Data Fixtures uses DELETE statements to drop the existing rows from
the database. If you want to use a TRUNCATE statement instead you can use the <info>--purge-with-truncate</info> flag:

  <info>doctrine:fixtures:load --purge-with-truncate</info>
');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var EntityManager $em */
        $em = $this->managerRegistry->getManager($input->getOption('em'));

        $append = (bool) $input->getOption('append');

        if ($input->isInteractive() && !$append && !$this->askConfirmation(
            $input,
            $output,
            '<question>Careful, database will be purged. Do you want to continue y/N ?</question>',
            false
        )) {
            return 1;
        }

        $dirOrFile = $input->getOption('fixtures');

        if (\is_string($dirOrFile) || (\is_array($dirOrFile) && $dirOrFile !== [])) {
            $paths = \is_array($dirOrFile) ? $dirOrFile : [$dirOrFile];
            $this->loader->loadPaths($paths);
        } else {
            $this->loader->load();
            $paths = $this->loader->getPaths();
        }

        $fixtures = $this->loader->getFixtures();
        if ($fixtures === []) {
            throw new InvalidArgumentException(sprintf(
                'Could not find any fixtures to load in: %s',
                "\n\n- " . implode("\n- ", $paths)
            ));
        }

        $purgeWithTruncate = (bool) $input->getOption('purge-with-truncate');

        $purger = new ORMPurger($em);
        $purger->setPurgeMode(
            $purgeWithTruncate ? ORMPurger::PURGE_MODE_TRUNCATE : ORMPurger::PURGE_MODE_DELETE
        );

        $executor = new ORMExecutor($em, $purger);
        $executor->setLogger(
            static function ($message) use ($output): void {
                $output->writeln(sprintf('  <comment>></comment> <info>%s</info>', $message));
            }
        );
        $executor->execute($fixtures, $input->getOption('append'));

        return 0;
    }

    private function askConfirmation(
        InputInterface $input,
        OutputInterface $output,
        string $question,
        bool $default,
    ): bool {
        $helperSet = $this->getHelperSet();
        assert($helperSet instanceof HelperSet);

        $questionHelper = $helperSet->get('question');
        assert($questionHelper instanceof QuestionHelper);

        $question = new ConfirmationQuestion($question, $default);

        $answer = $questionHelper->ask($input, $output, $question);

        assert(is_bool($answer));

        return $answer;
    }
}
