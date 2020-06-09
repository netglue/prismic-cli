<?php
declare(strict_types=1);

namespace Primo\Cli\Exception;

use Primo\Cli\Type\Spec;
use RuntimeException;
use Throwable;

use function sprintf;

class BuildError extends RuntimeException
{
    public static function unknown(Throwable $previous) : self
    {
        return new static(sprintf(
            'An unknown build error occurred: %s',
            $previous->getMessage()
        ), 500, $previous);
    }

    public static function notArray(Spec $type, string $source) : self
    {
        return new static(sprintf(
            'The source file for "%s" did not return an array suitable for serialisation. Path: %s',
            $type->name(),
            $source
        ));
    }
}
