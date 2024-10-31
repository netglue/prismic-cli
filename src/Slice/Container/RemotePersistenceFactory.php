<?php

declare(strict_types=1);

namespace Primo\Cli\Slice\Container;

use Primo\Cli\Slice\RemotePersistence;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\SharedSliceManagementClient;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class RemotePersistenceFactory
{
    public function __invoke(ContainerInterface $container): RemotePersistence
    {
        $client = $container->get(Client::class);
        if (! $client instanceof SharedSliceManagementClient) {
            throw new RuntimeException('Ensure that the doctype client version is >= 1.5.0');
        }

        return new RemotePersistence($client);
    }
}
