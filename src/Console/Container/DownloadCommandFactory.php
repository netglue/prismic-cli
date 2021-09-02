<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\DownloadCommand;
use Primo\Cli\Type\LocalPersistence;
use Primo\Cli\Type\RemotePersistence;
use Psr\Container\ContainerInterface;

final class DownloadCommandFactory
{
    public function __invoke(ContainerInterface $container): DownloadCommand
    {
        return new DownloadCommand(
            $container->get(LocalPersistence::class),
            $container->get(RemotePersistence::class)
        );
    }
}
