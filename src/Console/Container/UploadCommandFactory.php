<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\UploadCommand;
use Primo\Cli\Type\LocalPersistence;
use Primo\Cli\Type\RemotePersistence;
use Psr\Container\ContainerInterface;

final class UploadCommandFactory
{
    public function __invoke(ContainerInterface $container): UploadCommand
    {
        return new UploadCommand(
            $container->get(LocalPersistence::class),
            $container->get(RemotePersistence::class),
        );
    }
}
