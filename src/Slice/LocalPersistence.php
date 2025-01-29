<?php

declare(strict_types=1);

namespace Primo\Cli\Slice;

use Primo\Cli\Exception\PersistenceError;
use Prismic\DocumentType\SharedSlice;

use function assert;
use function closedir;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_readable;
use function is_resource;
use function opendir;
use function readdir;
use function sprintf;
use function str_ends_with;
use function substr;

use const DIRECTORY_SEPARATOR;

final class LocalPersistence implements SlicePersistence
{
    public function __construct(private readonly SliceBuildConfig $config)
    {
    }

    public function has(string $id): bool
    {
        return file_exists(sprintf(
            '%s%s%s.json',
            $this->config->distDir,
            DIRECTORY_SEPARATOR,
            $id,
        ));
    }

    public function read(string $id): SharedSlice
    {
        if (! $this->has($id)) {
            throw PersistenceError::readFailure();
        }

        $data = file_get_contents(sprintf(
            '%s%s%s.json',
            $this->config->distDir,
            DIRECTORY_SEPARATOR,
            $id,
        ));

        assert($data !== '' && $data !== false);

        return SharedSlice::new($id, $data);
    }

    public function write(SharedSlice $definition): void
    {
        file_put_contents(sprintf(
            '%s%s%s.json',
            $this->config->distDir,
            DIRECTORY_SEPARATOR,
            $definition->id,
        ), $definition->json);
    }

    /** @inheritDoc */
    public function all(): iterable
    {
        $list = [];
        $handle = opendir($this->config->distDir);
        assert(is_resource($handle));

        while (($filename = readdir($handle)) !== false) {
            $path = sprintf('%s%s%s', $this->config->distDir, DIRECTORY_SEPARATOR, $filename);
            if (! is_file($path) || ! is_readable($path)) {
                continue;
            }

            if (! str_ends_with($filename, '.json')) {
                continue;
            }

            $id = substr($filename, 0, -5);
            assert($id !== '');

            $list[] = $this->read($id);
        }

        closedir($handle);

        return $list;
    }
}
