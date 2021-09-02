<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\Exception\PersistenceError;
use Primo\Cli\Type\TypePersistence;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function sprintf;

use const PHP_EOL;

final class DownloadCommand extends Command
{
    public const DEFAULT_NAME = 'primo:download';

    /** @var TypePersistence */
    private $local;
    /** @var TypePersistence */
    private $remote;

    public function __construct(TypePersistence $local, TypePersistence $remote, string $name = self::DEFAULT_NAME)
    {
        $this->local = $local;
        $this->remote = $remote;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Download one or all document type definitions');
        $this->setHelp(
            'This command iterates over all your configured Prismic types and downloads them to the ' . PHP_EOL .
            'configured `dist` directory.' . PHP_EOL .
            'You can optionally provide a single type identifier to download just one of the configured types.'
        );

        $this->addArgument('type', InputArgument::OPTIONAL, 'An individual type identifier to download', null);
        $this->addOption('no-index', null, InputOption::VALUE_NONE, 'Disable index.json generation');
        $this->addOption('all', 'a', InputOption::VALUE_NONE, 'Include disabled types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');

        try {
            $types = $type !== null
                ? [$this->remote->read($type)]
                : $this->remote->all();
        } catch (PersistenceError $error) {
            $style->error($error->getMessage());

            return self::FAILURE;
        }

        $writeIndex = $input->getOption('no-index') === false;
        $skipDisabled = $input->getOption('all') !== false;

        foreach ($types as $type) {
            if ($skipDisabled && ! $type->isActive()) {
                continue;
            }

            $style->comment(sprintf('Writing "%s"', $type->label()));
            try {
                $this->local->write($type);
            } catch (PersistenceError $error) {
                $style->error(sprintf('Failed to write "%s" to local storage', $type->label()));

                return self::FAILURE;
            }
        }

        if ($writeIndex) {
            try {
                $style->comment('Writing Index');
                $this->local->writeIndex($this->remote->indexSpecs());
            } catch (PersistenceError $error) {
                $style->error('Failed to write the index to local storage');

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
