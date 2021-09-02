<?php

declare(strict_types=1);

namespace Primo\Cli\Exception;

use RuntimeException;

use function sprintf;

final class FilesystemError extends RuntimeException
{
    public static function missingDirectory(string $path): self
    {
        return new self(sprintf(
            'The directory "%s" either does not exist, or is not a directory',
            $path
        ));
    }

    public static function notReadable(string $path): self
    {
        return new self(sprintf(
            'The path "%s" is not readable',
            $path
        ));
    }

    public static function notWritable(string $path): self
    {
        return new self(sprintf(
            'The path "%s" is not writable',
            $path
        ));
    }
}
