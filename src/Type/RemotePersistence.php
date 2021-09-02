<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use Primo\Cli\Exception\PersistenceError;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\Definition;
use Prismic\DocumentType\Exception\DefinitionNotFound;
use Throwable;
use Traversable;

use function array_filter;
use function array_map;
use function iterator_to_array;

final class RemotePersistence implements TypePersistence
{
    /** @var Client */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function has(string $id): bool
    {
        try {
            $this->client->getDefinition($id);

            return true;
        } catch (DefinitionNotFound $error) {
            return false;
        } catch (Throwable $error) {
            throw PersistenceError::readFailure($error);
        }
    }

    public function read(string $id): Definition
    {
        try {
            return $this->client->getDefinition($id);
        } catch (Throwable $error) {
            throw PersistenceError::readFailure($error);
        }
    }

    public function write(Definition $definition): void
    {
        try {
            $this->client->saveDefinition($definition);
        } catch (Throwable $error) {
            throw PersistenceError::writeFailure($error);
        }
    }

    /** @inheritDoc */
    public function all(): iterable
    {
        try {
            return $this->client->fetchAllDefinitions();
        } catch (Throwable $error) {
            throw PersistenceError::readFailure($error);
        }
    }

    /** @inheritDoc */
    public function indexSpecs(): iterable
    {
        try {
            $types = $this->client->fetchAllDefinitions();
        } catch (Throwable $error) {
            throw PersistenceError::readFailure($error);
        }

        $types = $types instanceof Traversable ? iterator_to_array($types) : $types;
        $types = array_filter($types, static function (Definition $definition): bool {
            return $definition->isActive();
        });

        return array_map(static function (Definition $definition): Spec {
            return Spec::new(
                $definition->id(),
                $definition->label(),
                $definition->isRepeatable()
            );
        }, $types);
    }

    /** @inheritDoc */
    public function writeIndex(iterable $specs): void
    {
        // It is not possible to write an index to remote storage
    }
}
