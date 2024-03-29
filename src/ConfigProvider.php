<?php

declare(strict_types=1);

namespace Primo\Cli;

final class ConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->dependencies(),
            'console' => [
                'commands' => $this->commands(),
            ],
            'laminas-cli' => [
                'commands' => $this->commands(),
            ],
            'primo' => [
                'cli' => [
                    'builder' => [
                        'source' => null, // Path to directory containing 1 php file per type
                        'dist' => null, // Path to directory to store JSON output files
                    ],
                ],
                /**
                 * Types should look something like this:
                 * [
                 *     'id' => 'some-type',
                 *     'name' => 'My Document Type',
                 *     'repeatable' => true,
                 * ],
                 * ... etc
                 */
                'types' => [],
            ],
        ];
    }

    /** @return array<string, array<class-string, class-string>> */
    private function dependencies(): array
    {
        return [
            'factories' => [
                Console\BuildCommand::class => Console\Container\BuildCommandFactory::class,
                Type\LocalPersistence::class => Type\Container\LocalPersistenceFactory::class,
                BuildConfig::class => Container\BuildConfigFactory::class,
            ],
        ];
    }

    /** @return array<string, class-string> */
    private function commands(): array
    {
        return [
            Console\BuildCommand::DEFAULT_NAME => Console\BuildCommand::class,
        ];
    }
}
