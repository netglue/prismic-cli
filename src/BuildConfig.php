<?php

declare(strict_types=1);

namespace Primo\Cli;

use Primo\Cli\Exception\FilesystemError;
use Primo\Cli\Exception\InvalidArgument;
use Primo\Cli\Type\Spec;

use function array_values;
use function file_exists;
use function is_dir;
use function is_readable;
use function is_writable;
use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final class BuildConfig
{
    private string $sourceDirectory;
    private string $distDirectory;
    /** @var array<array-key, Spec> */
    private array $types = [];

    /** @param array<array-key, Spec> $types */
    private function __construct(string $sourceDirectory, string $distDirectory, array $types)
    {
        $this->setSourceDir($sourceDirectory);
        $this->setDistDir($distDirectory);
        foreach ($types as $type) {
            $this->addTypeSpec($type);
        }

        $this->types = array_values($this->types);
    }

    /** @param array<array-key, Spec> $types */
    public static function with(string $sourceDir, string $distDir, array $types): self
    {
        return new self($sourceDir, $distDir, $types);
    }

    /** @param iterable<array-key, array{id: string, name: string, repeatable: bool}> $types */
    public static function withArraySpecs(string $sourceDir, string $distDir, iterable $types): self
    {
        $specs = [];
        foreach ($types as $spec) {
            $specs[] = Spec::new($spec['id'], $spec['name'], $spec['repeatable'] ?? true);
        }

        return self::with($sourceDir, $distDir, $specs);
    }

    public function sourceDirectory(): string
    {
        return $this->sourceDirectory;
    }

    public function distDirectory(): string
    {
        return $this->distDirectory;
    }

    /** @return array<array-key, Spec> */
    public function types(): array
    {
        return $this->types;
    }

    private function setSourceDir(string $source): void
    {
        $source = rtrim($source, DIRECTORY_SEPARATOR);
        $this->assertDirectory($source);
        $this->assertReadable($source);
        $this->sourceDirectory = $source;
    }

    private function setDistDir(string $dist): void
    {
        $dist = rtrim($dist, DIRECTORY_SEPARATOR);
        $this->assertDirectory($dist);
        $this->assertWritable($dist);
        $this->distDirectory = $dist;
    }

    private function assertDirectory(string $directory): void
    {
        if (! file_exists($directory) || ! is_dir($directory)) {
            throw FilesystemError::missingDirectory($directory);
        }
    }

    private function assertReadable(string $path): void
    {
        if (! is_readable($path)) {
            throw FilesystemError::notReadable($path);
        }
    }

    private function assertWritable(string $path): void
    {
        if (! is_writable($path)) {
            throw FilesystemError::notWritable($path);
        }
    }

    private function addTypeSpec(Spec $type): void
    {
        if ($type->id() === 'index') {
            throw InvalidArgument::indexDisallowed($type);
        }

        $path = sprintf('%s%s%s', $this->sourceDirectory(), DIRECTORY_SEPARATOR, $type->source());
        $this->assertReadable($path);
        $this->types[$type->id()] = $type;
    }
}
