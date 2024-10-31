<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\DeleteCommand;
use Prismic\DocumentType\Client;
use Psr\Container\ContainerInterface;

final class DeleteCommandFactory
{
    public function __invoke(ContainerInterface $container): DeleteCommand
    {
        return new DeleteCommand(
            $container->get(Client::class),
        );
    }
}
