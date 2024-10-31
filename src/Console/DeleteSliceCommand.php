<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Prismic\DocumentType\SharedSliceManagementClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function is_string;

#[AsCommand('primo:slices:delete')]
final class DeleteSliceCommand extends Command
{
    public const DEFAULT_NAME = 'primo:slices:delete';

    public function __construct(
        private SharedSliceManagementClient $apiClient,
        string $name = self::DEFAULT_NAME,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(
            'This command deletes the specified shared slice',
        );

        $this->addArgument(
            'id',
            InputArgument::REQUIRED,
            'The slice id to delete',
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');
        if (! is_string($id) || $id === '') {
            $style->error('The id argument is required');

            return self::FAILURE;
        }

        $this->apiClient->deleteSharedSlice($id);
        $style->success('Shared slice deleted successfully');

        return self::SUCCESS;
    }
}
