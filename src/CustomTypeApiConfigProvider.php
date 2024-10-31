<?php

declare(strict_types=1);

namespace Primo\Cli;

use Prismic;

final class CustomTypeApiConfigProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'primo' => [
                'custom-type-api' => [
                    'token' => null, // A permanent access token for the custom types api.
                    'repository' => null, // The name of the repository - not a URL, just the name.
                ],
            ],
            'dependencies' => $this->dependencies(),
            'console' => [
                'commands' => $this->commands(),
            ],
            'laminas-cli' => [
                'commands' => $this->commands(),
            ],
        ];
    }

    /** @return array<string, array<string, string>> */
    private function dependencies(): array
    {
        return [
            'factories' => [
                Prismic\DocumentType\BaseClient::class => Container\CustomTypeClientFactory::class,

                Console\DeleteCommand::class => Console\Container\DeleteCommandFactory::class,
                Console\DeleteSliceCommand::class => Console\Container\DeleteSliceCommandFactory::class,
                Console\DiffCommand::class => Console\Container\DiffCommandFactory::class,
                Console\DownloadCommand::class => Console\Container\DownloadCommandFactory::class,
                Console\DownloadSlicesCommand::class => Console\Container\DownloadSlicesCommandFactory::class,
                Console\ListSlicesCommand::class => Console\Container\ListSlicesCommandFactory::class,
                Console\UploadCommand::class => Console\Container\UploadCommandFactory::class,
                Console\UploadSlicesCommand::class => Console\Container\UploadSlicesCommandFactory::class,

                Slice\RemotePersistence::class => Slice\Container\RemotePersistenceFactory::class,

                Type\RemotePersistence::class => Type\Container\RemotePersistenceFactory::class,

                DiffTool::class => Container\DiffToolFactory::class,
            ],
            'aliases' => [
                Prismic\DocumentType\Client::class => Prismic\DocumentType\BaseClient::class,
            ],
        ];
    }

    /** @return array<string, class-string> */
    private function commands(): array
    {
        return [
            Console\DeleteCommand::DEFAULT_NAME => Console\DeleteCommand::class,
            Console\DeleteSliceCommand::DEFAULT_NAME => Console\DeleteSliceCommand::class,
            Console\DiffCommand::DEFAULT_NAME => Console\DiffCommand::class,
            Console\DownloadCommand::DEFAULT_NAME => Console\DownloadCommand::class,
            Console\DownloadSlicesCommand::DEFAULT_NAME => Console\DownloadSlicesCommand::class,
            Console\ListSlicesCommand::DEFAULT_NAME => Console\ListSlicesCommand::class,
            Console\UploadCommand::DEFAULT_NAME => Console\UploadCommand::class,
            Console\UploadSlicesCommand::DEFAULT_NAME => Console\UploadSlicesCommand::class,
        ];
    }
}
