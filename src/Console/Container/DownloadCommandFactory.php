<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\Console\DownloadCommand;
use Primo\Cli\TypePersister;
use Psr\Container\ContainerInterface;

final class DownloadCommandFactory
{
    public function __invoke(ContainerInterface $container): DownloadCommand
    {
        return new DownloadCommand(
            $container->get(TypePersister::class),
            $container->get(BuildConfig::class)
        );
    }
}
