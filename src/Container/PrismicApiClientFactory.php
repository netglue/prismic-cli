<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\Assert;
use Prismic\Api;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;

final class PrismicApiClientFactory
{
    use HttpComponentDiscovery;

    public function __invoke(ContainerInterface $container): ApiClient
    {
        $config = $container->has('config') ? $container->get('config') : [];
        Assert::isArrayAccessible($config);
        $prismic = $config['prismic'] ?? [];
        Assert::isArray($prismic);
        $apiUrl = $prismic['api'] ?? null;
        $token = $prismic['token'] ?? null;
        Assert::string(
            $apiUrl,
            'An api url cannot be determined. Your content repository url should be available in ' .
                'configuration under [prismic][api] and should be a non-empty string.',
        );

        Assert::nullOrStringNotEmpty($token);

        return Api::get(
            $apiUrl,
            $token,
            $this->httpClient($container),
            $this->requestFactory($container),
            $this->uriFactory($container),
        );
    }
}
