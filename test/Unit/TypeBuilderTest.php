<?php

declare(strict_types=1);

namespace PrimoTest\Cli\Unit;

use PHPUnit\Framework\TestCase;
use Primo\Cli\TypeBuilder as T;

class TypeBuilderTest extends TestCase
{
    public function testLinkConfigHasExpectedStructure(): void
    {
        $data = T::link('Label', 'Placeholder', false, null);
        $expect = [
            'type' => T::TYPE_LINK,
            'config' => [
                'select' => null,
                'label' => 'Label',
                'placeholder' => 'Placeholder',
            ],
        ];
        self::assertEquals($expect, $data);
    }
}
