<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use Primo\Cli\Exception\PersistenceError;
use Prismic\DocumentType\Definition;

interface TypePersistence
{
    /**
     * Whether the type is currently persisted
     *
     * @throws PersistenceError if a problem occurs querying the underlying storage.
     */
    public function has(string $id): bool;

    /**
     * Retrieve the type definition by its id
     *
     * @throws PersistenceError if a problem occurs reading from the underlying storage.
     */
    public function read(string $id): Definition;

    /**
     * Write the definition to storage
     *
     * @throws PersistenceError if a problem occurs writing to the underlying storage.
     */
    public function write(Definition $definition): void;

    /**
     * Retrieve a list of known types
     *
     * @return iterable<Definition>
     *
     * @throws PersistenceError if a problem occurs reading from the underlying storage.
     */
    public function all(): iterable;

    /**
     * Return a list of type specs for creating indexes
     *
     * @return iterable<Spec>
     *
     * @throws PersistenceError if a problem occurs reading from the underlying storage.
     */
    public function indexSpecs(): iterable;

    /**
     * Write an index file to storage
     *
     * @param iterable<Spec> $specs
     *
     * @throws PersistenceError if a problem occurs writing to the underlying storage.
     */
    public function writeIndex(iterable $specs): void;
}
