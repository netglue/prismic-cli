<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\BuildConfig;
use Primo\Cli\Type\Spec;
use Psr\Container\ContainerInterface;

class BuildConfigFactory
{
    public function __invoke(ContainerInterface $container): BuildConfig
    {
        $config = $container->has('config') ? $container->get('config') : [];
        $dirs = $config['primo']['cli']['builder'] ?? [];
        $types = $config['primo']['types'] ?? [];
        $specs = [];
        foreach ($types as $spec) {
            $specs[] = Spec::new($spec['id'] ?? null, $spec['name'] ?? null, $spec['repeatable'] ?? true);
        }

        return BuildConfig::with($dirs['source'], $dirs['dist'], $specs);
    }
}
