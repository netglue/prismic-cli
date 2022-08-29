<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\Assert;
use Primo\Cli\Exception\PersistenceError;
use Primo\Cli\Type\TypePersistence;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;
use function sprintf;

use const PHP_EOL;

final class UploadCommand extends Command
{
    public const DEFAULT_NAME = 'primo:types:upload';

    private TypePersistence $local;
    private TypePersistence $remote;

    public function __construct(TypePersistence $local, TypePersistence $remote, string $name = self::DEFAULT_NAME)
    {
        $this->local = $local;
        $this->remote = $remote;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Upload one or all configured document type definitions');
        $this->setHelp(
            'This command iterates over all your configured Prismic types and uploads them to the remote ' .
            'custom types api endpoint, making them immediately usable in your Prismic repository.' . PHP_EOL .
            'You can optionally provide a single type identifier to upload just one of the configured types.',
        );

        $this->addArgument('type', InputArgument::OPTIONAL, 'An individual type identifier to upload', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $type = $input->getArgument('type');
        Assert::nullOrStringNotEmpty($type);

        try {
            $types = is_string($type)
                ? [$this->local->read($type)]
                : $this->local->all();
        } catch (PersistenceError $error) {
            $style->error('Failed to read local type definitions - make sure they have been built first');

            return self::FAILURE;
        }

        foreach ($types as $type) {
            $style->comment(sprintf('Uploading "%s"', $type->label()));
            try {
                $this->remote->write($type);
            } catch (PersistenceError $error) {
                $style->error(sprintf('Upload of "%s" failed', $type->label()));

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }
}
