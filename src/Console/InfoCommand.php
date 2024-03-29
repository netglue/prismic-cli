<?php

declare(strict_types=1);

namespace Primo\Cli\Console;

use Primo\Cli\Assert;
use Prismic\ApiClient;
use Prismic\Document;
use Prismic\Value\Bookmark;
use Prismic\Value\Language;
use Prismic\Value\Type;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function array_map;
use function implode;
use function sprintf;

use const PHP_EOL;

final class InfoCommand extends Command
{
    public const DEFAULT_NAME = 'primo:info';

    public function __construct(private ApiClient $apiClient, string $name = self::DEFAULT_NAME)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription(
            'This command shows information about a specific document when given its unique identifier, '
            . 'or, with no arguments provides information about the repository.',
        );
        $this->addArgument(
            'id',
            InputArgument::OPTIONAL,
            'The unique id of the document',
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');
        Assert::nullOrStringNotEmpty($id);
        if ($id === null) {
            $this->showApiInfo($style);

            return 0;
        }

        $document = $this->apiClient->findById($id);
        if (! $document) {
            $style->warning(sprintf(
                'A document could not be found with the id "%s" in the repository "%s"',
                $id,
                $this->apiClient->host(),
            ));

            return 0;
        }

        $this->showDocumentInfo($style, $document);

        return 0;
    }

    private function showDocumentInfo(SymfonyStyle $style, Document $document): void
    {
        $style->title(sprintf('Document Information: %s', $document->id()));

        $style->table([], [
            ['ID:', $document->id()],
            ['UID:', $document->uid() ?? '<none>'],
            ['Type:', $document->type()],
            ['Created At:', $document->firstPublished()->format('l jS F Y H:i:s')],
            ['Modified At:', $document->lastPublished()->format('l jS F Y H:i:s')],
            ['Language:', $document->lang()],
            ['Tags:', $document->tags() === [] ? '<none>' : implode(', ', $document->tags())],
        ]);
    }

    /**
     * @psalm-suppress DeprecatedClass, DeprecatedMethod
     * @todo Remove bookmark list in 2.0
     */
    private function showApiInfo(SymfonyStyle $style): void
    {
        $style->title(sprintf('Repository Information: %s', $this->apiClient->host()));
        $style->table([], [
            ['Repository Host:', $this->apiClient->host()],
            ['Master Ref:', $this->apiClient->ref()->ref()],
            [
                'Document Types:',
                implode(PHP_EOL, array_map(static function (Type $type): string {
                    return sprintf('%s ("%s")', $type->id(), $type->name());
                }, $this->apiClient->data()->types())),
            ],
            [
                'Languages:',
                implode(PHP_EOL, array_map(static function (Language $language): string {
                    return $language->name();
                }, $this->apiClient->data()->languages())),
            ],
            [
                'Bookmarks:',
                implode(PHP_EOL, array_map(static function (Bookmark $bookmark): string {
                    return sprintf('%s (%s)', $bookmark->name(), $bookmark->documentId());
                }, $this->apiClient->data()->bookmarks())),
            ],
        ]);
    }
}
