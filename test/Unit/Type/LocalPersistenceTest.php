<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit\Type;

use PHPUnit\Framework\TestCase;
use Primo\Cli\BuildConfig;
use Primo\Cli\Exception\PersistenceError;
use Primo\Cli\Type\LocalPersistence;
use Primo\Cli\Type\Spec;
use Prismic\DocumentType\Definition;

use function assert;
use function glob;
use function realpath;
use function sprintf;
use function unlink;

class LocalPersistenceTest extends TestCase
{
    private BuildConfig $config;
    private LocalPersistence $storage;
    private string $sourceDir;
    private string $distDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sourceDir = __DIR__ . '/../build-specs/src';
        $this->distDir = __DIR__ . '/../build-specs/dist';
        $this->config = BuildConfig::with(
            $this->sourceDir,
            $this->distDir,
            [
                Spec::new(
                    'example',
                    'Example',
                    true,
                ),
            ],
        );
        $this->storage = new LocalPersistence($this->config);
    }

    protected function tearDown(): void
    {
        $glob = sprintf('%s/*.json', realpath($this->distDir));
        assert($glob !== '');

        foreach (glob($glob) as $file) {
            unlink($file);
        }
    }

    public function testHasReturnsTrueForAKnownSpec(): void
    {
        self::assertTrue($this->storage->has('example'));
    }

    public function testHasReturnsFalseForUnknownSpec(): void
    {
        self::assertFalse($this->storage->has('unknown'));
    }

    public function testIndexSpecReturnsExpectedList(): void
    {
        $value = $this->storage->indexSpecs();
        self::assertCount(1, $value);
        self::assertContainsOnlyInstancesOf(Spec::class, $value);
    }

    public function testThatTheIndexCanBeWritten(): void
    {
        $target = sprintf('%s/index.json', $this->distDir);
        self::assertFileDoesNotExist($target);
        $this->storage->writeIndex($this->storage->indexSpecs());
        self::assertFileExists($target);
    }

    public function testReadIsExceptionalIfDistIsNotBuilt(): void
    {
        $this->expectException(PersistenceError::class);
        $this->storage->read('example');
    }

    public function testAllIsExceptionalIfDistIsNotBuilt(): void
    {
        $this->expectException(PersistenceError::class);
        $this->storage->all();
    }

    public function testWriteWillCreateAFile(): void
    {
        $definition = Definition::new(
            'example',
            'Example',
            true,
            true,
            'Some Content',
        );

        $target = sprintf('%s/example.json', $this->distDir);
        self::assertFileDoesNotExist($target);
        $this->storage->write($definition);
        self::assertFileExists($target);
    }

    public function testThatDefinitionsNotFoundInConfigurationCanBeWritten(): void
    {
        $definition = Definition::new(
            'unknown',
            'Example',
            true,
            true,
            'Some Content',
        );

        self::assertFalse($this->storage->has('unknown'));

        $target = sprintf('%s/unknown.json', $this->distDir);
        self::assertFileDoesNotExist($target);
        $this->storage->write($definition);
        self::assertFileExists($target);
    }

    public function testThatOnceWrittenDefinitionsCanBeRead(): void
    {
        $input = Definition::new(
            'example',
            'Example',
            true,
            true,
            'Some Content',
        );

        $this->storage->write($input);
        $output = $this->storage->read('example');

        self::assertNotSame($input, $output);
        self::assertTrue($input->equals($output));
    }
}
