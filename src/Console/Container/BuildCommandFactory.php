<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\Console\BuildCommand;
use Primo\Cli\Slice\LocalPersistence as LocalSlicePersistence;
use Primo\Cli\Slice\SliceBuildConfig;
use Primo\Cli\Type\LocalPersistence;
use Psr\Container\ContainerInterface;

final class BuildCommandFactory
{
    public function __invoke(ContainerInterface $container): BuildCommand
    {
        return new BuildCommand(
            $container->get(BuildConfig::class),
            $container->get(LocalPersistence::class),
            $container->get(SliceBuildConfig::class),
            $container->get(LocalSlicePersistence::class),
        );
    }
}
