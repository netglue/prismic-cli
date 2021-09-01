<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\BuildConfig;
use Primo\Cli\TypePersister;
use Prismic\DocumentType\Exception\DefinitionNotFound;
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

    /** @var TypePersister */
    private $persister;
    /** @var BuildConfig */
    private $config;

    public function __construct(TypePersister $uploader, BuildConfig $config, string $name = self::DEFAULT_NAME)
    {
        $this->persister = $uploader;
        $this->config = $config;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Download one or all document type definitions');
        $this->setHelp(sprintf(
            'This command iterates over all your configured Prismic types and downloads them to the ' . PHP_EOL .
            'configured `dist` directory (%s).' . PHP_EOL .
            'You can optionally provide a single type identifier to download just one of the configured types.',
            $this->config->distDirectory()
        ));

        $this->addArgument('type', InputArgument::OPTIONAL, 'An individual type identifier to download', null);
        $this->addOption('no-index', null, InputOption::VALUE_NONE, 'Disable index.json generation');
        $this->addOption('all', 'a', InputOption::VALUE_NONE, 'Include disabled types');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $single = $input->getArgument('type');
        if ($single !== null) {
            return $this->downloadOne($single, $style);
        }

        $writeIndex = $input->getOption('no-index') === false;
        $skipDisabled = $input->getOption('all') !== false;

        $this->persister->download($writeIndex, $skipDisabled);

        $style->success('Types definitions saved');

        return self::SUCCESS;
    }

    private function downloadOne(string $single, SymfonyStyle $style): int
    {
        try {
            $this->persister->downloadType($single);
        } catch (DefinitionNotFound $error) {
            $style->error(sprintf('The type "%s" does not exist', $single));

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
