<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use PHPUnit\Framework\TestCase;
use Primo\Cli\ConfigProvider;

class ConfigProviderTest extends TestCase
{
    /** @test */
    public function theConfigProviderShouldReturnAnArray(): void
    {
        self::assertIsArray(
            (new ConfigProvider())(),
        );
    }
}
