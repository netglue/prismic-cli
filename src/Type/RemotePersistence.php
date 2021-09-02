<?php

declare(strict_types=1);

namespace Primo\Cli\Type;

use Prismic\DocumentType\Client;
use Prismic\DocumentType\Definition;
use Prismic\DocumentType\Exception\DefinitionNotFound;

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
        }
    }

    public function read(string $id): Definition
    {
        return $this->client->getDefinition($id);
    }

    public function write(Definition $definition): void
    {
        $this->client->saveDefinition($definition);
    }

    public function all(): iterable
    {
        return $this->client->fetchAllDefinitions();
    }
}
