<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\TypePersister;
use Prismic\DocumentType\Client;
use Psr\Container\ContainerInterface;

final class TypePersisterFactory
{
    public function __invoke(ContainerInterface $container): TypePersister
    {
        return new TypePersister(
            $container->get(BuildConfig::class),
            $container->get(Client::class)
        );
    }
}
