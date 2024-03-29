<?php

declare(strict_types=1);

namespace Primo\Cli;

use Prismic\DocumentType\Definition;
use SebastianBergmann\Diff\Differ;

use function json_decode;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;

final class DiffTool
{
    public function __construct(private Differ $differ)
    {
    }

    public function diff(Definition $left, Definition $right): string|null
    {
        $left = $this->prettyPrint($left);
        $right = $this->prettyPrint($right);

        if ($left === $right) {
            return null;
        }

        return $this->differ->diff($left, $right);
    }

    private function prettyPrint(Definition $definition): string
    {
        return json_encode(
            json_decode($definition->json(), true, 512, JSON_THROW_ON_ERROR),
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT,
        );
    }
}
