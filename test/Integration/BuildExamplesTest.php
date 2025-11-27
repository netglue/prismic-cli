<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Integration;

use PHPUnit\Framework\TestCase;
use Primo\Cli\BuildConfig;
use Primo\Cli\Console\BuildCommand;
use Primo\Cli\Slice\LocalPersistence as SlicePersistence;
use Primo\Cli\Slice\SliceBuildConfig;
use Primo\Cli\Type\LocalPersistence;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

final class BuildExamplesTest extends TestCase
{
    /** @var array<array-key, array{id: non-empty-string, name: string, repeatable: bool}> */
    private static array $types = [
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
    ];

    public function testThatNoErrorsOccurWhenProcessingTheExampleConfiguration(): void
    {
        $this->expectNotToPerformAssertions();
        $config = BuildConfig::withArraySpecs(
            __DIR__ . '/../../example/source',
            __DIR__ . '/../../example/dist',
            self::$types,
        );

        $sliceConfig = SliceBuildConfig::withDirectories(
            __DIR__ . '/../../example/slices/source',
            __DIR__ . '/../../example/slices/dist',
        );

        $application = new Application('Type Builder Example');
        $application->add(new BuildCommand(
            $config,
            new LocalPersistence($config),
            $sliceConfig,
            new SlicePersistence($sliceConfig),
        ));
        $application->setAutoExit(false);
        $application->setDefaultCommand(BuildCommand::DEFAULT_NAME, true);
        $application->run(new ArgvInput(['', '-qn']));
    }
}
