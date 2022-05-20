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

    public function testThatNumbersWillIncludeTheMinWhenZero(): void
    {
        $data = T::number('Number', 'Placeholder', 0, 100);
        $expect = [
            'type' => T::TYPE_NUMBER,
            'config' => [
                'label' => 'Number',
                'placeholder' => 'Placeholder',
                'min' => 0,
                'max' => 100,
            ],
        ];
        self::assertEquals($expect, $data);
    }
}
