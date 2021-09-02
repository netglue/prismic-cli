<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit\Type;

use PHPUnit\Framework\TestCase;
use Primo\Cli\Type\Spec;

use function json_encode;

class SpecTest extends TestCase
{
    /** @var Spec */
    private $spec;

    protected function setUp(): void
    {
        parent::setUp();
        $this->spec = Spec::new('page', 'Web Page', true);
    }

    public function testSource(): void
    {
        $this->assertSame('page.php', $this->spec->source());
    }

    public function testFilename(): void
    {
        $this->assertSame('page.json', $this->spec->filename());
    }

    public function testName(): void
    {
        $this->assertSame('Web Page', $this->spec->name());
    }

    public function testId(): void
    {
        $this->assertSame('page', $this->spec->id());
    }

    public function testSerialize(): void
    {
        $expect = '{"id":"page","name":"Web Page","repeatable":true,"value":"page.json"}';
        $this->assertJsonStringEqualsJsonString($expect, json_encode($this->spec));
    }

    public function testRepeatable(): void
    {
        self::assertTrue($this->spec->repeatable());
    }
}
