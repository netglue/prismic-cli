<?php
// phpcs:ignoreFile
declare(strict_types=1);

namespace Primo\Cli;

use Primo\Cli\Exception\AssertionFailed;
use Webmozart\Assert\Assert as WebmozartAssert;

final class Assert extends WebmozartAssert
{
    /**
     * @param string $message
     *
     * @return never
     *
     * @throws AssertionFailed
     *
     * @psalm-pure this method is not supposed to perform side-effects
     */
    protected static function reportInvalidArgument($message)
    {
        throw new AssertionFailed($message);
    }
}
