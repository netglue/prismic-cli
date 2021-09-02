<?php

declare(strict_types=1);

namespace Primo\Cli\Type\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\Type\LocalPersistence;
use Psr\Container\ContainerInterface;

final class LocalPersistenceFactory
{
    public function __invoke(ContainerInterface $container): LocalPersistence
    {
        return new LocalPersistence($container->get(BuildConfig::class));
    }
}
