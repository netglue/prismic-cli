<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Override;
use Primo\Cli\Assert;
use Prismic\DocumentType\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

use function sprintf;

#[AsCommand('primo:types:delete')]
final class DeleteCommand extends Command
{
    public const DEFAULT_NAME = 'primo:types:delete';

    public function __construct(private readonly Client $apiClient, string $name = self::DEFAULT_NAME)
    {
        parent::__construct($name);
    }

    #[Override]
    protected function configure(): void
    {
        $this->setDescription(
            'This command deletes a single document type. ',
        );
        $this->addArgument(
            'type',
            InputArgument::REQUIRED,
            'The document type to delete',
        );
    }

    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        Assert::nullOrStringNotEmpty($type);

        if ($type === null) {
            $style->error('The type to delete must be specified');

            return self::FAILURE;
        }

        try {
            $this->apiClient->deleteDefinition($type);
        } catch (Throwable $error) {
            $style->error(sprintf('Failed to delete the type: %s', $error->getMessage()));

            return self::FAILURE;
        }

        $style->success(sprintf(
            'Document type "%s" deleted successfully',
            $type,
        ));

        return self::SUCCESS;
    }
}
