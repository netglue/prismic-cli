<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\Assert;
use Prismic\ApiClient;
use Prismic\Document;
use Prismic\Predicate;
use Prismic\Value\Type;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_map;
use function sprintf;

final class ListCommand extends Command
{
    public const DEFAULT_NAME = 'primo:list';

    public function __construct(private ApiClient $apiClient, string $name = self::DEFAULT_NAME)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(
            'This command lists documents of a specific type. '
            . 'With no arguments, it lists types available in the repository.',
        );
        $this->addArgument(
            'type',
            InputArgument::OPTIONAL,
            'The document type to list',
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $type = $input->getArgument('type');
        Assert::nullOrStringNotEmpty($type);

        if ($type === null) {
            $this->listTypes($style);

            return 0;
        }

        $list = $this->apiClient->findAll(
            $this->apiClient->createQuery()
                ->query(Predicate::at('document.type', $type)),
        );

        $formatDocument = static function (Document $document): array {
            return [
                $document->id(),
                $document->uid(),
                $document->lastPublished()->format('Y-m-d H:i:s'),
                $document->lang(),
            ];
        };

        $style->title(sprintf('Found %d documents with the type %s', $list->count(), $type));
        $style->table(
            ['ID', 'UID', 'Last Modified', 'Language'],
            array_map($formatDocument, $list->results()),
        );

        return 0;
    }

    private function listTypes(SymfonyStyle $style): void
    {
        $style->title(sprintf('Types available in %s', $this->apiClient->host()));
        $style->table(['Type'], array_map(static function (Type $type): array {
            return [sprintf('%s (%s)', $type->id(), $type->name())];
        }, $this->apiClient->data()->types()));
    }
}
