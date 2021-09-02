<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use Primo\Cli\DiffTool;
use PHPUnit\Framework\TestCase;
use Prismic\DocumentType\Definition;
use SebastianBergmann\Diff\Differ;

class DiffToolTest extends TestCase
{
    /** @var DiffTool */
    private $tool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tool = new DiffTool(new Differ());
    }

    public function testThatTheDifferencesCanBeDiffed(): void
    {
        $left = Definition::new('foo', 'bar', true, true, '{"foo":["a","b","c"]}');
        $right = Definition::new('foo', 'bar', true, true, '{"foo":["a","b","d"]}');

        $diff = $this->tool->diff($left, $right);

        $expect = <<<DIFF
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
