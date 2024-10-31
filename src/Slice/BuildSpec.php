<?php

declare(strict_types=1);

namespace Primo\Cli\Slice;

final class BuildSpec
{
    /**
     * @param non-empty-string $source
     * @param non-empty-string $id
     */
    public function __construct(
        public readonly string $source,
        public readonly string $id,
    ) {
    }
}
