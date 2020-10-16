<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\Exception\ConfigurationError;
use Prismic\Api;
use Prismic\ApiClient;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

final class PrismicApiClientFactory
{
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
            $container->has(ClientInterface::class) ? $container->get(ClientInterface::class) : null,
            $container->has(RequestFactoryInterface::class) ? $container->get(RequestFactoryInterface::class) : null,
            $container->has(UriFactoryInterface::class) ? $container->get(UriFactoryInterface::class) : null
        );
    }
}
