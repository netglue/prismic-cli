<?php

declare(strict_types=1);

namespace Primo\Cli\Exception;

use InvalidArgumentException;
use Primo\Cli\Type\Spec;

use function sprintf;

class InvalidArgument extends InvalidArgumentException
{
    final public static function indexDisallowed(Spec $spec): self
    {
        return new self(sprintf(
            'The type definition for "%s" has "index" for its identifier. This is not allowed because "index.json" is used ' .
            'to inform Prismic about the type definitions',
            $spec->name()
        ));
    }
}
