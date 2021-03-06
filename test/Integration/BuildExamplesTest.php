<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Integration;

use PHPUnit\Framework\TestCase;
use Primo\Cli\BuildConfig;
use Primo\Cli\Console\BuildCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

final class BuildExamplesTest extends TestCase
{
    /** @var array<string, mixed>[] */
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
        $config = BuildConfig::withArraySpecs(
            __DIR__ . '/../../example/source',
            __DIR__ . '/../../example/dist',
            self::$types
        );

        $application = new Application('Type Builder Example');
        $application->add(new BuildCommand($config));
        $application->setAutoExit(false);
        $application->setDefaultCommand(BuildCommand::DEFAULT_NAME, true);
        $application->run(new ArgvInput(['', '-qn']));
        $this->addToAssertionCount(1);
    }
}
