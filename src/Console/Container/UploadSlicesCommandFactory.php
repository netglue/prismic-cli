<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\UploadSlicesCommand;
use Primo\Cli\Slice\LocalPersistence;
use Primo\Cli\Slice\RemotePersistence;
use Psr\Container\ContainerInterface;

final class UploadSlicesCommandFactory
{
    public function __invoke(ContainerInterface $container): UploadSlicesCommand
    {
        return new UploadSlicesCommand(
            $container->get(LocalPersistence::class),
            $container->get(RemotePersistence::class),
        );
    }
}
