<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use Primo\Cli\Assert;
use Primo\Cli\BuildConfig;
use Primo\Cli\Exception\InvalidArgument;
use Prismic\DocumentType\Definition;
use Traversable;

use function array_map;
use function file_get_contents;
use function file_put_contents;
use function iterator_to_array;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final class LocalPersistence implements TypePersistence
{
    /** @var BuildConfig */
    private $config;

    public function __construct(
        BuildConfig $config
    ) {
        $this->config = $config;
    }

    public function has(string $id): bool
    {
        try {
            $this->getSpec($id);

            return true;
        } catch (InvalidArgument $e) {
            return false;
        }
    }

    public function read(string $id): Definition
    {
        $spec = $this->getSpec($id);
        $path = $this->path($spec);
        Assert::fileExists($path);
        Assert::readable($path);

        return Definition::new(
            $spec->id(),
            $spec->name(),
            $spec->repeatable(),
            true,
            file_get_contents($path)
        );
    }

    public function write(Definition $definition): void
    {
        $spec = $this->has($definition->id())
            ? $this->getSpec($definition->id())
            : $this->specFromDefinition($definition);

        $path = $this->path($spec);
        file_put_contents($path, $definition->json());
    }

    private function path(Spec $spec): string
    {
        return sprintf(
            '%s%s%s',
            $this->config->distDirectory(),
            DIRECTORY_SEPARATOR,
            $spec->filename()
        );
    }

    private function getSpec(string $id): Spec
    {
        foreach ($this->config->types() as $type) {
            if ($type->id() !== $id) {
                continue;
            }

            return $type;
        }

        throw new InvalidArgument(sprintf('The type "%s" is not locally defined', $id));
    }

    private function specFromDefinition(Definition $definition): Spec
    {
        return Spec::new(
            $definition->id(),
            $definition->label(),
            $definition->isRepeatable()
        );
    }

    /** @inheritDoc */
    public function all(): iterable
    {
        $types = $this->config->types();
        $types = $types instanceof Traversable ? iterator_to_array($types): $types;

        return array_map(function (Spec $spec): Definition {
            return $this->read($spec->id());
        }, $types);
    }
}
