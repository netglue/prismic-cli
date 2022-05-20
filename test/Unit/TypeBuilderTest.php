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

    public function testWidthOrHeightWillYieldImageConstraintInRichText(): void
    {
        $data = T::richText('Foo', 'Foo', [], true, true, true, [], 999, null);
        $expect = ['width' => 999];

        self::assertArrayHasKey('imageConstraint', $data['config']);
        self::assertEquals($expect, $data['config']['imageConstraint']);

        $data = T::richText('Foo', 'Foo', [], true, true, true, [], null, 999);
        $expect = ['height' => 999];

        self::assertArrayHasKey('imageConstraint', $data['config']);
        self::assertEquals($expect, $data['config']['imageConstraint']);

        $data = T::richText('Foo', 'Foo', [], true, true, true, [], 123, 456);
        $expect = ['width' => 123, 'height' => 456];

        self::assertArrayHasKey('imageConstraint', $data['config']);
        self::assertEquals($expect, $data['config']['imageConstraint']);

        $data = T::richText('Foo', 'Foo', []);
        self::assertArrayNotHasKey('imageConstraint', $data['config']);
    }
}
