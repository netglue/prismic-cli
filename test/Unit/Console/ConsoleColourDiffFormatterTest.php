<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit\Console;

use PHPUnit\Framework\TestCase;
use Primo\Cli\Console\ConsoleColourDiffFormatter;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder;

final class ConsoleColourDiffFormatterTest extends TestCase
{
    public function testSimpleDiff(): void
    {
        $left = <<<'TEXT'
            Mary had
            a little
            lamb, it's
            fleece was white
            as snow
            TEXT;

        $right = <<<'TEXT'
            Mary had
            a little
            goat, it's
            fleece was white
            as flour.
            TEXT;

        $differ = new Differ(new DiffOnlyOutputBuilder());
        $diff = $differ->diff($left, $right);

        $formatter = new ConsoleColourDiffFormatter();

        $expect = <<<'DIFF'
            <comment>    ---------- begin diff ----------</comment>
            <fg=red>-lamb, it's</fg=red>
            <fg=green>+goat, it's</fg=green>
            <fg=red>-as snow</fg=red>
            <fg=green>+as flour.</fg=green>
            <comment>    ----------- end diff -----------</comment>
            
            DIFF;

        self::assertEquals($expect, $formatter->format($diff));
    }
}
