<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Assert;
use Primo\Cli\Console\ListCommand;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

final class ListCommandFactory
{
    public function __invoke(ContainerInterface $container): ListCommand
    {
        $localApiClient = $container->get('PrimoCliApiInstance');
        Assert::isInstanceOf($localApiClient, ApiClient::class);

        return new ListCommand($localApiClient);
    }
}
