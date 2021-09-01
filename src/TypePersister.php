<?php

declare(strict_types=1);

namespace Primo\Cli;

use Primo\Cli\Type\Spec;
use Prismic\DocumentType\Client;
use Prismic\DocumentType\Definition;
use Prismic\DocumentType\Exception\DefinitionNotFound;
use Prismic\DocumentType\Json;

use function file_get_contents;
use function file_put_contents;
use function json_encode;
use function sprintf;

use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

final class TypePersister
{
    /** @var BuildConfig */
    private $config;
    /** @var Client */
    private $client;

    public function __construct(
        BuildConfig $config,
        Client $client
    ) {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Iterates over all of the configured types and posts each to the remote API
     */
    public function upload(): void
    {
        $types = $this->config->types();

        foreach ($types as $type) {
            $this->uploadType($type);
        }
    }

    /**
     * Reads the content of the json file on disk for the given spec and POSTs it to the remote API
     */
    public function uploadType(Spec $type): void
    {
        $directory = $this->config->distDirectory();
        $path = sprintf('%s%s%s', $directory, DIRECTORY_SEPARATOR, $type->source());
        Assert::fileExists($path);
        Assert::readable($path);
        $fileContent = file_get_contents($path);

        // A round trip encode(decode) ensures consistent whitespace for equality checks:
        $json = Json::encodeArray(Json::decodeToArray($fileContent));

        $definition = Definition::new(
            $type->id(),
            $type->name(),
            $type->repeatable(),
            true,
            $json
        );

        $this->client->saveDefinition($definition);
    }

    public function download(bool $writeIndex = true, bool $skipDisabled = true): void
    {
        $types = $this->client->fetchAllDefinitions();
        $specs = [];

        foreach ($types as $type) {
            if ($skipDisabled && ! $type->isActive()) {
                continue;
            }

            $this->writeDefinition($type);
            $specs[] = Spec::new(
                $type->id(),
                $type->label(),
                $type->isRepeatable()
            );
        }

        if (! $writeIndex) {
            return;
        }

        $indexPath = sprintf('%s%sindex.json', $this->config->distDirectory(), DIRECTORY_SEPARATOR);
        file_put_contents($indexPath, json_encode($specs, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }

    /**
     * @throws DefinitionNotFound if the requested id does not exist.
     */
    public function downloadType(string $id): void
    {
        $definition = $this->client->getDefinition($id);
        $this->writeDefinition($definition);
    }

    private function writeDefinition(Definition $definition): void
    {
        $destination = sprintf(
            '%s%s%s.json',
            $this->config->distDirectory(),
            DIRECTORY_SEPARATOR,
            $definition->id()
        );

        $jsonString = json_encode(Json::decodeToArray($definition->json()), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        file_put_contents($destination, $jsonString);
    }
}
