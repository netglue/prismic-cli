<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\InfoCommand;
use Psr\Container\ContainerInterface;

final class InfoCommandFactory
{
    public function __invoke(ContainerInterface $container): InfoCommand
    {
        return new InfoCommand($container->get('PrimoCliApiInstance'));
    }
}
