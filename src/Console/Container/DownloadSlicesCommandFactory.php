<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\DownloadSlicesCommand;
use Primo\Cli\Slice\LocalPersistence;
use Primo\Cli\Slice\RemotePersistence;
use Psr\Container\ContainerInterface;

final class DownloadSlicesCommandFactory
{
    public function __invoke(ContainerInterface $container): DownloadSlicesCommand
    {
        return new DownloadSlicesCommand(
            $container->get(LocalPersistence::class),
            $container->get(RemotePersistence::class),
        );
    }
}
