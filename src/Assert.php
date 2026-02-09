<?php

declare(strict_types=1);

namespace Primo\Cli;

use Override;
use Primo\Cli\Exception\AssertionFailed;
use Webmozart\Assert\Assert as WebmozartAssert;

/** @internal */
final class Assert extends WebmozartAssert
{
    /**
     * @throws AssertionFailed
     *
     * @psalm-pure this method is not supposed to perform side-effects
     */
    #[Override]
    protected static function reportInvalidArgument(string $message): never
    {
        throw new AssertionFailed($message);
    }
}
