<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\Console\BuildCommand;
use Psr\Container\ContainerInterface;

class BuildCommandFactory
{
    public function __invoke(ContainerInterface $container): BuildCommand
    {
        return new BuildCommand($container->get(BuildConfig::class));
    }
}
