<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\BuildConfig;
use Primo\Cli\Exception\InvalidArgument;
use Primo\Cli\Type\Spec;
use Primo\Cli\TypePersister;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;
use function sprintf;

use const PHP_EOL;

final class UploadCommand extends Command
{
    public const DEFAULT_NAME = 'primo:upload';

    /** @var TypePersister */
    private $uploader;
    /** @var BuildConfig */
    private $config;

    public function __construct(TypePersister $uploader, BuildConfig $config, string $name = self::DEFAULT_NAME)
    {
        $this->uploader = $uploader;
        $this->config = $config;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Upload one or all configured document type definitions');
        $this->setHelp(
            'This command iterates over all your configured Prismic types and uploads them to the remote ' .
            'custom types api endpoint, making them immediately usable in your Prismic repository.' . PHP_EOL .
            'You can optionally provide a single type identifier to upload just one of the configured types.'
        );

        $this->addArgument('type', InputArgument::OPTIONAL, 'An individual type identifier to upload', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $specs = $this->resolveTypes($input->getArgument('type'));

        $style->progressStart(count($specs));
        foreach ($specs as $spec) {
            $this->uploader->uploadType($spec);
        }

        $style->progressFinish();

        $style->success(sprintf(
            '%d document types uploaded',
            count($specs)
        ));

        return self::SUCCESS;
    }

    /**
     * @return array<Spec>
     */
    private function resolveTypes(?string $type): array
    {
        $specs = $this->config->types();
        if ($type === null) {
            return $specs;
        }

        foreach ($specs as $spec) {
            if ($spec->id() !== $type) {
                continue;
            }

            return [$spec];
        }

        throw new InvalidArgument(sprintf('The type "%s" is not a known type', $type));
    }
}
