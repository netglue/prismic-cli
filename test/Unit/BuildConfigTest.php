<?php
declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use PHPUnit\Framework\TestCase;
use Primo\Cli\BuildConfig;
use Primo\Cli\Exception\FilesystemError;
use Primo\Cli\Exception\InvalidArgument;
use Primo\Cli\Type\Spec;

use function sprintf;

class BuildConfigTest extends TestCase
{
    /** @var BuildConfig */
    private $config;

    protected function setUp() : void
    {
        parent::setUp();

        $this->config = BuildConfig::withArraySpecs(
            __DIR__ . '/../../example/source',
            __DIR__ . '/../../example/dist',
            [
                [
                    'id' => 'page',
                    'name' => 'A Web Page',
                    'repeatable' => true,
                ],
                [
                    'id' => 'error',
                    'name' => 'An Error Page',
                    'repeatable' => true,
                ],
                [
                    'id' => 'article',
                    'name' => 'Blog Post',
                    'repeatable' => true,
                ],
            ]
        );
    }

    public function testExceptionThrownForNonDirectorySource() : void
    {
        $this->expectException(FilesystemError::class);
        $this->expectExceptionMessage(sprintf('The directory "%s" either does not exist, or is not a directory', __FILE__));
        BuildConfig::with(__FILE__, '', []);
    }

    public function testExceptionThrownForNonDirectoryDest() : void
    {
        $this->expectException(FilesystemError::class);
        $this->expectExceptionMessage(sprintf('The directory "%s" either does not exist, or is not a directory', __FILE__));
        BuildConfig::with(__DIR__, __FILE__, []);
    }

    public function testNoExceptionThrownForEmptyTypes() : void
    {
        BuildConfig::with(__DIR__, __DIR__, []);
        $this->addToAssertionCount(1);
    }

    public function testDirectoryAccessors() : void
    {
        $config = BuildConfig::with(__DIR__, __DIR__, []);
        $this->assertSame(__DIR__, $config->sourceDirectory());
        $this->assertSame(__DIR__, $config->distDirectory());
    }

    public function testMissingSourceFilesAreExceptional() : void
    {
        $types = [
            ['id' => 'a', 'name' => 'b', 'repeatable' => true],
        ];
        $this->expectException(FilesystemError::class);
        $this->expectExceptionMessage('is not readable');
        BuildConfig::withArraySpecs(__DIR__, __DIR__, $types);
    }

    public function testWithArrayTypes() : void
    {
        $this->assertContainsOnlyInstancesOf(Spec::class, $this->config->types());
    }

    public function testTypesCannotBeCalledIndex() : void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('has "index" for its identifier');
        BuildConfig::withArraySpecs(__DIR__, __DIR__, [
            ['id' => 'index', 'name' => 'foo', 'repeatable' => true],
        ]);
    }
}
