<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\Assert;
use Primo\Cli\Exception\PersistenceError;
use Primo\Cli\Slice\SlicePersistence;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;
use function sprintf;

#[AsCommand('primo:slices:download')]
final class DownloadSlicesCommand extends Command
{
    public const DEFAULT_NAME = 'primo:slices:download';

    public function __construct(
        private readonly SlicePersistence $local,
        private readonly SlicePersistence $remote,
        string $name = self::DEFAULT_NAME,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Download one or all of your shared slice definitions');
        $this->addArgument('id', InputArgument::OPTIONAL, 'An individual identifier to download', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');
        Assert::nullOrStringNotEmpty($id);

        try {
            $slices = is_string($id)
                ? [$this->remote->read($id)]
                : $this->remote->all();
        } catch (PersistenceError) {
            $style->error('Failed to read remote slices');

            return self::FAILURE;
        }

        foreach ($slices as $slice) {
            $style->comment(sprintf('Downloading "%s"', $slice->id));
            try {
                $this->local->write($slice);
            } catch (PersistenceError) {
                $style->error(sprintf('Download of "%s" failed', $slice->id));

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
