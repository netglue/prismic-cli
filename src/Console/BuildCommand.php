<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\BuildConfig;
use Primo\Cli\Exception\BuildError;
use Primo\Cli\Type\Spec;
use Primo\Cli\Type\TypePersistence;
use Prismic\DocumentType\Definition;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function count;
use function is_array;
use function json_encode;
use function sprintf;

use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const PHP_EOL;

final class BuildCommand extends Command
{
    public const DEFAULT_NAME = 'primo:build';

    public function __construct(
        private BuildConfig $config,
        private TypePersistence $localStorage,
        string $name = self::DEFAULT_NAME,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Build JSON document models from local PHP Sources');
        $this->setHelp(
            'This command iterates over all your configured Prismic types and renders the json into a file ' .
            'for each type in the configured output directory.' . PHP_EOL .
            'There are no arguments or parameters.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $types = $this->config->types();
        $style->progressStart(count($types) + 1);
        foreach ($this->config->types() as $spec) {
            $this->buildType($spec);
            $style->progressAdvance(1);
        }

        $this->localStorage->writeIndex($types);
        $style->progressAdvance(1);
        $style->progressFinish();

        $style->success(sprintf(
            '%d document types processed',
            count($types),
        ));

        return self::SUCCESS;
    }

    private function buildType(Spec $type): void
    {
        $source = sprintf('%s%s%s', $this->config->sourceDirectory(), DIRECTORY_SEPARATOR, $type->source());

        try {
            /** @psalm-suppress UnresolvableInclude */
            $data = require $source;
        } catch (Throwable $error) {
            throw BuildError::unknown($error);
        }

        if (! is_array($data)) {
            throw BuildError::notArray($type, $source);
        }

        try {
            $content = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        } catch (Throwable $error) {
            throw BuildError::unknown($error);
        }

        $this->localStorage->write(Definition::new(
            $type->id(),
            $type->name(),
            $type->repeatable(),
            true,
            $content,
        ));
    }
}
