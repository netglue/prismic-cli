<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use Prismic\DocumentType\Definition;

interface TypePersistence
{
    /**
     * Whether the type is currently persisted
     */
    public function has(string $id): bool;

    /**
     * Retrieve the type definition by its id
     */
    public function read(string $id): Definition;

    /**
     * Write the definition to storage
     */
    public function write(Definition $definition): void;

    /**
     * Retrieve a list of known types
     *
     * @return iterable<Definition>
     */
    public function all(): iterable;
}
