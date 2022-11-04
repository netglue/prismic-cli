<?php

declare(strict_types=1);

namespace Primo\Cli\Exception;

use RuntimeException;
use Throwable;

final class PersistenceError extends RuntimeException
{
    public static function readFailure(Throwable|null $previous = null): self
    {
        return new self('An error occurred reading from storage', 0, $previous);
    }

    public static function writeFailure(Throwable|null $previous = null): self
    {
        return new self('An error occurred writing to storage', 0, $previous);
    }
}
