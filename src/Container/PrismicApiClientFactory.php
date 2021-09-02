<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\Exception\ConfigurationError;
use Prismic\Api;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

final class PrismicApiClientFactory
{
    use HttpComponentDiscovery;

    public function __invoke(ContainerInterface $container): ApiClient
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $apiUrl = $config['prismic']['api'] ?? null;
        if (empty($apiUrl)) {
            throw new ConfigurationError(
                'An api url cannot be determined. Your content repository url should be available in ' .
                'configuration under [prismic][api] and should be a non-empty string.'
            );
        }

        return Api::get(
            $apiUrl,
            $config['prismic']['token'] ?? null,
            $this->httpClient($container),
            $this->requestFactory($container),
            $this->uriFactory($container)
        );
    }
}
