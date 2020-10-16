<?php

declare(strict_types=1);

namespace Primo\Cli;

final class ApiToolsConfigProvider
{
    /** @return mixed */
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
        ];
    }

    /** @return mixed[] */
    private function dependencies(): array
    {
        return [
            'factories' => [
                'PrimoCliApiInstance' => Container\PrismicApiClientFactory::class,
                Console\InfoCommand::class => Console\Container\InfoCommandFactory::class,
                Console\ListCommand::class => Console\Container\ListCommandFactory::class,
            ],
        ];
    }

    /** @return string[] */
    private function commands(): array
    {
        return [
            Console\InfoCommand::DEFAULT_NAME => Console\InfoCommand::class,
            Console\ListCommand::DEFAULT_NAME => Console\ListCommand::class,
        ];
    }
}
