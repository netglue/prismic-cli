<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\ListSlicesCommand;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\SharedSliceManagementClient;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class ListSlicesCommandFactory
{
    public function __invoke(ContainerInterface $container): ListSlicesCommand
    {
        $client = $container->get(Client::class);
        if (! $client instanceof SharedSliceManagementClient) {
            throw new RuntimeException('Ensure that the doctype client version is >= 1.5.0');
        }

        return new ListSlicesCommand(
            $client,
        );
    }
}
