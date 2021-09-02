<?php

declare(strict_types=1);

namespace Primo\Cli\Type\Container;

use Primo\Cli\Type\RemotePersistence;
use Prismic\DocumentType\Client;
use Psr\Container\ContainerInterface;

final class RemotePersistenceFactory
{
    public function __invoke(ContainerInterface $container): RemotePersistence
    {
        return new RemotePersistence($container->get(Client::class));
    }
}
