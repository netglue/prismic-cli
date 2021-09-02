<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\DiffTool;
use Primo\Cli\Type\TypePersistence;
use Prismic\DocumentType\Definition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;
use function sprintf;

use const PHP_EOL;

final class DiffCommand extends Command
{
    public const DEFAULT_NAME = 'primo:diff';

    /** @var ConsoleColourDiffFormatter */
    private $formatter;
    /** @var DiffTool */
    private $diffTool;
    /** @var TypePersistence */
    private $local;
    /** @var TypePersistence */
    private $remote;

    public function __construct(
        DiffTool $diffTool,
        ConsoleColourDiffFormatter $formatter,
        TypePersistence $localStorage,
        TypePersistence $remoteStorage,
        string $name = self::DEFAULT_NAME
    ) {
        $this->diffTool = $diffTool;
        $this->formatter = $formatter;
        $this->local = $localStorage;
        $this->remote = $remoteStorage;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Diff local changes to document models against the remote versions');
        $this->setHelp(
            'This command iterates over all your configured Prismic types and displays a unified diff ' . PHP_EOL .
            'between the local version and the version on the remote.' . PHP_EOL .
            'You can optionally provide a single type identifier to diff just one of the configured types.' . PHP_EOL .
            'The command returns 0 if there are no changes and 1 if there are differences between local and remote.'
        );

        $this->addArgument('type', InputArgument::OPTIONAL, 'An individual type identifier to diff', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        $types = is_string($type)
            ? [$this->local->read($type)]
            : $this->local->all();

        $returnValue = self::SUCCESS;

        foreach ($types as $local) {
            $style->section(sprintf('Changes in %s.json', $local->id()));

            if (! $this->remote->has($local->id())) {
                $this->newLocalType($local, $style);
                $returnValue = self::FAILURE;

                continue;
            }

            $remote = $this->remote->read($local->id());


            $diff = $this->diffTool->diff($local, $remote);
            if ($diff === null) {
                $this->identical($local, $style);

                continue;
            }

            $this->formatDiff($diff, $style);
            $returnValue = self::FAILURE;
        }

        return $returnValue;
    }

    private function newLocalType(Definition $type, SymfonyStyle $style): void
    {
        $style->info(sprintf(
            "%s.json is not present in the remote API",
            $type->id()
        ));
    }

    private function identical(Definition $local, SymfonyStyle $style): void
    {
        $style->info(sprintf(
            "%s.json is unchanged",
            $local->id()
        ));
    }

    private function formatDiff(string $diff, SymfonyStyle $style): void
    {

        $style->write(
            $this->formatter->format($diff)
        );
    }
}
