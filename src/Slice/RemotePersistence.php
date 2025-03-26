<?php

declare(strict_types=1);

namespace Primo\Cli\Slice;

use Override;
use Primo\Cli\Exception\PersistenceError;
use Prismic\DocumentType\Exception\DefinitionNotFound;
use Prismic\DocumentType\Exception\Exception;
use Prismic\DocumentType\SharedSlice;
use Prismic\DocumentType\SharedSliceManagementClient;
use Throwable;

final class RemotePersistence implements SlicePersistence
{
    public function __construct(private readonly SharedSliceManagementClient $client)
    {
    }

    #[Override]
    public function has(string $id): bool
    {
        try {
            $this->client->getSharedSlice($id);

            return true;
        } catch (DefinitionNotFound) {
            return false;
        } catch (Exception $error) {
            throw PersistenceError::readFailure($error);
        }
    }

    #[Override]
    public function read(string $id): SharedSlice
    {
        try {
            return $this->client->getSharedSlice($id);
        } catch (Exception $error) {
            throw PersistenceError::readFailure($error);
        }
    }

    #[Override]
    public function write(SharedSlice $definition): void
    {
        try {
            $this->client->saveSharedSlice($definition);
        } catch (Exception $error) {
            throw PersistenceError::writeFailure($error);
        }
    }

    /** @inheritDoc */
    #[Override]
    public function all(): iterable
    {
        try {
            return $this->client->fetchAllSharedSlices();
        } catch (Throwable $error) {
            throw PersistenceError::readFailure($error);
        }
    }
}
