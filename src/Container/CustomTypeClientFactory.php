<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\Assert;
use Prismic\DocumentType\BaseClient;
use Psr\Container\ContainerInterface;

final class CustomTypeClientFactory
{
    use HttpComponentDiscovery;

    public function __invoke(ContainerInterface $container): BaseClient
    {
        $config = $container->get('config');
        Assert::isArrayAccessible($config);
        $primo = $config['primo'] ?? [];
        Assert::isArrayAccessible($primo);
        $apiConfig = $primo['custom-type-api'] ?? [];
        Assert::isArrayAccessible($apiConfig);

        $token = $apiConfig['token'] ?? null;
        $repository = $apiConfig['repository'] ?? null;
        Assert::string($token, 'An access token must be provided in config.primo.custom-type-api.token in order to access the Prismic custom type API.');
        Assert::string($repository, 'The repository name must be provided in config.primo.custom-type-api.repository in order to access the Prismic custom type API.');

        return new BaseClient(
            $token,
            $repository,
            $this->httpClient($container),
            $this->requestFactory($container),
            $this->uriFactory($container),
            $this->streamFactory($container)
        );
    }
}
