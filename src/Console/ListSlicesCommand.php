<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Prismic\DocumentType\SharedSlice;
use Prismic\DocumentType\SharedSliceManagementClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Traversable;

use function array_map;
use function assert;
use function count;
use function is_array;
use function is_string;
use function iterator_to_array;
use function json_decode;
use function sprintf;

#[AsCommand('primo:slices:list')]
final class ListSlicesCommand extends Command
{
    public const DEFAULT_NAME = 'primo:slices:list';

    public function __construct(
        private SharedSliceManagementClient $apiClient,
        string $name = self::DEFAULT_NAME,
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(
            'This command lists shared slices. ',
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $slices = $this->apiClient->fetchAllSharedSlices();
        $slices = $slices instanceof Traversable ? iterator_to_array($slices, false) : $slices;

        $formatSlice = static function (SharedSlice $slice): array {
            $payload = json_decode($slice->json, true);
            assert(is_array($payload));
            $name = $payload['name'] ?? '';
            assert(is_string($name));
            $description = $payload['description'] ?? '';
            assert(is_string($description));
            $variations = $payload['variations'] ?? [];
            assert(is_array($variations));

            return [
                $slice->id,
                $name,
                $description,
                count($variations),
            ];
        };

        $style->title(sprintf('Found %d shared slices', count($slices)));
        $style->table(
            ['ID', 'Name', 'Description', 'Variations'],
            array_map($formatSlice, $slices),
        );

        return self::SUCCESS;
    }
}
