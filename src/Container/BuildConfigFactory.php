<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\Assert;
use Primo\Cli\BuildConfig;
use Psr\Container\ContainerInterface;

final class BuildConfigFactory
{
    public function __invoke(ContainerInterface $container): BuildConfig
    {
        $config = $container->has('config') ? $container->get('config') : [];
        Assert::isArrayAccessible($config);
        $primo = $config['primo'] ?? [];
        Assert::isArrayAccessible($primo);
        $cli = $primo['cli'] ?? [];
        Assert::isArrayAccessible($cli);
        $dirs = $cli['builder'] ?? [];
        Assert::isArrayAccessible($dirs);
        $types = $primo['types'] ?? [];
        Assert::isArrayAccessible($types);

        $source = $dirs['source'] ?? null;
        Assert::string($source);
        $dist = $dirs['dist'] ?? null;
        Assert::string($dist);

        /** @psalm-suppress PossiblyInvalidArgument */
        return BuildConfig::withArraySpecs($source, $dist, $types);
    }
}
