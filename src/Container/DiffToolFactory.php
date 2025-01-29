<?php

declare(strict_types=1);

namespace Primo\Cli\Container;

use Primo\Cli\DiffTool;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class DiffToolFactory
{
    public function __invoke(): DiffTool
    {
        return new DiffTool(
            new Differ(
                new UnifiedDiffOutputBuilder(),
            ),
        );
    }
}
