<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Primo\Cli\ConfigProvider;

final class ConfigProviderTest extends TestCase
{
    #[Test]
    public function theConfigProviderShouldReturnAnArray(): void
    {
        self::assertIsArray(
            (new ConfigProvider())(),
        );
    }
}
