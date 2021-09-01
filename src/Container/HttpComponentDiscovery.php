<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

trait HttpComponentDiscovery
{
    private function httpClient(ContainerInterface $container): ClientInterface
    {
        if ($container->has(ClientInterface::class)) {
            return $container->get(ClientInterface::class);
        }

        return Psr18ClientDiscovery::find();
    }

    private function requestFactory(ContainerInterface $container): RequestFactoryInterface
    {
        if ($container->has(RequestFactoryInterface::class)) {
            return $container->get(RequestFactoryInterface::class);
        }

        return Psr17FactoryDiscovery::findRequestFactory();
    }

    private function uriFactory(ContainerInterface $container): UriFactoryInterface
    {
        if ($container->has(UriFactoryInterface::class)) {
            return $container->get(UriFactoryInterface::class);
        }

        return Psr17FactoryDiscovery::findUriFactory();
    }

    private function streamFactory(ContainerInterface $container): StreamFactoryInterface
    {
        if ($container->has(StreamFactoryInterface::class)) {
            return $container->get(StreamFactoryInterface::class);
        }

        return Psr17FactoryDiscovery::findStreamFactory();
    }
}
