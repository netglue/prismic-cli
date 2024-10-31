<?php

declare(strict_types=1);

namespace Primo\Cli\Slice\Container;

use Primo\Cli\Assert;
use Primo\Cli\Slice\SliceBuildConfig;
use Psr\Container\ContainerInterface;

final class SliceBuildConfigFactory
{
    public function __invoke(ContainerInterface $container): SliceBuildConfig
    {
        $config = $container->get('config');
        Assert::isArrayAccessible($config);
        $primo = $config['primo'] ?? [];
        Assert::isArrayAccessible($primo);
        $cli = $primo['cli'] ?? [];
        Assert::isArrayAccessible($cli);

        $slices = $cli['slices'] ?? [];
        Assert::isArrayAccessible($slices);

        $source = $slices['source'] ?? null;
        Assert::stringNotEmpty($source);
        $dist = $slices['dist'] ?? null;
        Assert::stringNotEmpty($dist);

        return SliceBuildConfig::withDirectories($source, $dist);
    }
}
