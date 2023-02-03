<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\DiffTool;
use Psr\Container\ContainerInterface;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class DiffToolFactory
{
    public function __invoke(ContainerInterface $container): DiffTool
    {
        return new DiffTool(
            new Differ(
                new UnifiedDiffOutputBuilder(),
            ),
        );
    }
}
