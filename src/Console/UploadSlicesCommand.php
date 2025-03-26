<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Override;
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

use const PHP_EOL;

#[AsCommand('primo:slices:upload')]
final class UploadSlicesCommand extends Command
{
    public const DEFAULT_NAME = 'primo:slices:upload';

    public function __construct(
        private readonly SlicePersistence $local,
        private readonly SlicePersistence $remote,
        string $name = self::DEFAULT_NAME,
    ) {
        parent::__construct($name);
    }

    #[Override]
    protected function configure(): void
    {
        $this->setDescription('Upload one or all shared slice definitions');
        $this->setHelp(
            'This command iterates over all your shared slices and uploads them to the remote ' .
            'custom types api endpoint, making them immediately usable in your Prismic repository.' . PHP_EOL .
            'You can optionally provide a single identifier to upload just one of the configured slices.',
        );

        $this->addArgument('id', InputArgument::OPTIONAL, 'An individual identifier to upload', null);
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $id = $input->getArgument('id');
        Assert::nullOrStringNotEmpty($id);

        try {
            $slices = is_string($id)
                ? [$this->local->read($id)]
                : $this->local->all();
        } catch (PersistenceError) {
            $style->error('Failed to read local slices - make sure they have been built first');

            return self::FAILURE;
        }

        foreach ($slices as $slice) {
            $style->comment(sprintf('Uploading "%s"', $slice->id));
            try {
                $this->remote->write($slice);
            } catch (PersistenceError) {
                $style->error(sprintf('Upload of "%s" failed', $slice->id));

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
