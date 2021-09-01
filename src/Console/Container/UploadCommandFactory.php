<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\Console\UploadCommand;
use Primo\Cli\TypePersister;
use Psr\Container\ContainerInterface;

final class UploadCommandFactory
{
    public function __invoke(ContainerInterface $container): UploadCommand
    {
        return new UploadCommand(
            $container->get(TypePersister::class),
            $container->get(BuildConfig::class),
        );
    }
}
