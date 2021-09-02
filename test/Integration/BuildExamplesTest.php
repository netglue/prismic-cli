<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Integration;

use PHPUnit\Framework\TestCase;
use Primo\Cli\BuildConfig;
use Primo\Cli\Console\BuildCommand;
use Primo\Cli\Type\LocalPersistence;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

final class BuildExamplesTest extends TestCase
{
    /** @var array<array-key, array{id: string, name: string, repeatable: bool}> */
    private static $types = [
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
            self::$types
        );

        $application = new Application('Type Builder Example');
        $application->add(new BuildCommand($config, new LocalPersistence($config)));
        $application->setAutoExit(false);
        $application->setDefaultCommand(BuildCommand::DEFAULT_NAME, true);
        $application->run(new ArgvInput(['', '-qn']));
    }
}
