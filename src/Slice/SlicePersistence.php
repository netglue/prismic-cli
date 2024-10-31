<?php

declare(strict_types=1);

namespace Primo\Cli\Slice;

use Primo\Cli\Exception\PersistenceError;
use Prismic\DocumentType\SharedSlice;

interface SlicePersistence
{
    /**
     * Whether the slice is currently persisted
     *
     * @param non-empty-string $id
     *
     * @throws PersistenceError if a problem occurs querying the underlying storage.
     */
    public function has(string $id): bool;

    /**
     * Retrieve the slice definition by its id
     *
     * @param non-empty-string $id
     *
     * @throws PersistenceError if a problem occurs reading from the underlying storage.
     */
    public function read(string $id): SharedSlice;

    /**
     * Write the slice definition to storage
     *
     * @throws PersistenceError if a problem occurs writing to the underlying storage.
     */
    public function write(SharedSlice $definition): void;

    /**
     * Retrieve a list of known shared slices
     *
     * @return iterable<SharedSlice>
     *
     * @throws PersistenceError if a problem occurs reading from the underlying storage.
     */
    public function all(): iterable;
}
