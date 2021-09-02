<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\ConsoleColourDiffFormatter;
use Primo\Cli\Console\DiffCommand;
use Primo\Cli\DiffTool;
use Primo\Cli\Type\LocalPersistence;
use Primo\Cli\Type\RemotePersistence;
use Psr\Container\ContainerInterface;

final class DiffCommandFactory
{
    public function __invoke(ContainerInterface $container): DiffCommand
    {
        return new DiffCommand(
            $container->get(DiffTool::class),
            new ConsoleColourDiffFormatter(),
            $container->get(LocalPersistence::class),
            $container->get(RemotePersistence::class),
        );
    }
}
