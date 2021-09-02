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

    /** @return array<string, array<class-string, class-string>> */
    private function dependencies(): array
    {
        return [
            'factories' => [
                Prismic\DocumentType\BaseClient::class => Container\CustomTypeClientFactory::class,

                Console\DownloadCommand::class => Console\Container\DownloadCommandFactory::class,
                Console\UploadCommand::class => Console\Container\UploadCommandFactory::class,

                Type\LocalPersistence::class => Type\Container\LocalPersistenceFactory::class,
                Type\RemotePersistence::class => Type\Container\RemotePersistenceFactory::class,

                DiffTool::class => Container\DiffToolFactory::class,
                TypePersister::class => Container\TypePersisterFactory::class,
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
            Console\DownloadCommand::DEFAULT_NAME => Console\DownloadCommand::class,
            Console\UploadCommand::DEFAULT_NAME => Console\UploadCommand::class,
        ];
    }
}
