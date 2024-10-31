<?php

declare(strict_types=1);

namespace Primo\Cli\Slice\Container;

use Primo\Cli\Slice\LocalPersistence;
use Primo\Cli\Slice\SliceBuildConfig;
use Psr\Container\ContainerInterface;

final class LocalPersistenceFactory
{
    public function __invoke(ContainerInterface $container): LocalPersistence
    {
        return new LocalPersistence($container->get(SliceBuildConfig::class));
    }
}
