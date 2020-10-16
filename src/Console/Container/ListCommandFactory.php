<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\ListCommand;
use Psr\Container\ContainerInterface;

final class ListCommandFactory
{
    public function __invoke(ContainerInterface $container): ListCommand
    {
        return new ListCommand($container->get('PrimoCliApiInstance'));
    }
}
