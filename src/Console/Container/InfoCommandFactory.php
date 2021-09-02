<?php

declare(strict_types=1);

namespace Primo\Cli\Console\Container;

use Primo\Cli\Assert;
use Primo\Cli\Console\InfoCommand;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

final class InfoCommandFactory
{
    public function __invoke(ContainerInterface $container): InfoCommand
    {
        $localApiClient = $container->get('PrimoCliApiInstance');
        Assert::isInstanceOf($localApiClient, ApiClient::class);

        return new InfoCommand($localApiClient);
    }
}
