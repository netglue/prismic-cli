<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Console\DeleteSliceCommand;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\SharedSliceManagementClient;
use Psr\Container\ContainerInterface;
use RuntimeException;

final class DeleteSliceCommandFactory
{
    public function __invoke(ContainerInterface $container): DeleteSliceCommand
    {
        $client = $container->get(Client::class);
        if (! $client instanceof SharedSliceManagementClient) {
            throw new RuntimeException('Ensure that the doctype client version is >= 1.5.0');
        }

        return new DeleteSliceCommand(
            $client,
        );
    }
}
