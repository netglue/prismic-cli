<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use PHPUnit\Framework\TestCase;
use Primo\Cli\DiffTool;
use Prismic\DocumentType\Definition;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class DiffToolTest extends TestCase
{
    private DiffTool $tool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tool = new DiffTool(new Differ(new UnifiedDiffOutputBuilder()));
    }

    public function testThatTheDifferencesCanBeDiffed(): void
    {
        $left = Definition::new('foo', 'bar', true, true, '{"foo":["a","b","c"]}');
        $right = Definition::new('foo', 'bar', true, true, '{"foo":["a","b","d"]}');

        $diff = $this->tool->diff($left, $right);
        self::assertNotNull($diff);

        $expect = <<<'DIFF'
                 "foo": [
                     "a",
                     "b",
            -        "c"
            +        "d"
                 ]
            
            DIFF;

        self::assertStringContainsString($expect, $diff);
    }

    public function testThatIdenticalContentsWillReturnNullToSignifyZeroDifference(): void
    {
        $left = Definition::new('foo', 'bar', true, true, '{"foo":["a","b","c"]}');
        $diff = $this->tool->diff($left, $left);
        self::assertNull($diff);
    }
}
