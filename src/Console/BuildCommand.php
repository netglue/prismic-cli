<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Override;
use Primo\Cli\BuildConfig;
use Primo\Cli\Exception\BuildError;
use Primo\Cli\Slice\BuildSpec;
use Primo\Cli\Slice\SliceBuildConfig;
use Primo\Cli\Slice\SlicePersistence;
use Primo\Cli\Type\Spec;
use Primo\Cli\Type\TypePersistence;
use Prismic\DocumentType\Definition;
use Prismic\DocumentType\SharedSlice;
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
        private SliceBuildConfig $sliceConfig,
        private SlicePersistence $sliceStorage,
        string $name = self::DEFAULT_NAME,
    ) {
        parent::__construct($name);
    }

    #[Override]
    protected function configure(): void
    {
        $this->setDescription('Build JSON document models from local PHP Sources');
        $this->setHelp(
            'This command iterates over all your configured Prismic types and renders the json into a file ' .
            'for each type in the configured output directory.' . PHP_EOL .
            'There are no arguments or parameters.',
        );
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $types = $this->config->types();
        $slices = $this->sliceConfig->slices();
        $style->progressStart(count($types) + count($slices) + 1);

        $this->buildTypes($types, $style);
        $this->buildSlices($slices, $style);

        $this->localStorage->writeIndex($types);
        $style->progressAdvance(1);

        $style->progressFinish();

        $style->success(sprintf(
            '%d document types and %d shared slices processed',
            count($types),
            count($slices),
        ));

        return self::SUCCESS;
    }

    /** @param array<array-key, Spec> $types */
    private function buildTypes(array $types, SymfonyStyle $style): void
    {
        foreach ($types as $spec) {
            $this->buildType($spec);
            $style->progressAdvance(1);
        }
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

    /** @param list<BuildSpec> $slices */
    private function buildSlices(array $slices, SymfonyStyle $style): void
    {
        foreach ($slices as $spec) {
            $this->buildSlice($spec);
            $style->progressAdvance(1);
        }
    }

    private function buildSlice(BuildSpec $spec): void
    {
        try {
            /** @psalm-suppress UnresolvableInclude */
            $data = require $spec->source;
        } catch (Throwable $e) {
            throw BuildError::unknown($e);
        }

        if (! is_array($data)) {
            throw BuildError::invalidSliceSpec($spec);
        }

        try {
            $content = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
        } catch (Throwable $error) {
            throw BuildError::unknown($error);
        }

        $this->sliceStorage->write(SharedSlice::new($spec->id, $content));
    }
}
