<?php

declare(strict_types=1);

namespace Primo\Cli\Slice;

use Primo\Cli\Exception\FilesystemError;

use function assert;
use function closedir;
use function is_dir;
use function is_file;
use function is_readable;
use function is_writable;
use function opendir;
use function readdir;
use function rtrim;
use function sprintf;
use function str_ends_with;
use function substr;

use const DIRECTORY_SEPARATOR;

final class SliceBuildConfig
{
    /**
     * @param non-empty-string $sourceDir
     * @param non-empty-string $distDir
     */
    private function __construct(
        public readonly string $sourceDir,
        public readonly string $distDir,
    ) {
    }

    /**
     * @param non-empty-string $source
     * @param non-empty-string $dist
     */
    public static function withDirectories(
        string $source,
        string $dist,
    ): self {
        $source = rtrim($source, DIRECTORY_SEPARATOR);
        if (! is_dir($source) || $source === '') {
            throw FilesystemError::missingDirectory($source);
        }

        if (! is_readable($source)) {
            throw FilesystemError::notReadable($source);
        }

        $dist = rtrim($dist, DIRECTORY_SEPARATOR);
        if (! is_dir($dist) || $dist === '') {
            throw FilesystemError::missingDirectory($dist);
        }

        if (! is_writable($dist)) {
            throw FilesystemError::notWritable($dist);
        }

        return new self($source, $dist);
    }

    /** @return list<BuildSpec> */
    public function slices(): array
    {
        $list = [];

        $handle = opendir($this->sourceDir);

        while (($filename = readdir($handle)) !== false) {
            $path = sprintf(
                '%s%s%s',
                $this->sourceDir,
                DIRECTORY_SEPARATOR,
                $filename,
            );

            if (! is_file($path) || ! is_readable($path)) {
                continue;
            }

            if (! str_ends_with($filename, '.php')) {
                continue;
            }

            $id = substr($filename, 0, -4);
            assert($id !== '');

            $list[] = new BuildSpec($path, $id);
        }

        closedir($handle);

        return $list;
    }
}
